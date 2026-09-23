# Laravel Eloquent Relationships

This project is a small Laravel practice application for learning how different
types of Eloquent relationships work. The main models are `Student`, `Teacher`,
`Profile`, `Profile_detail`, `Comment`, `Like`, and `Subject`.

## Relationship Map

```text
Student 1 ---- 1 Profile
Teacher 1 ---- 1 Profile
Student 1 ---- many Comment
Teacher 1 ---- many Comment
Comment 1 ---- many Like
Student 1 ---- many Profile_detail (through Profile)
Student 1 ---- many Like (through Comment)
Student many ---- many Subject (polymorphic pivot)
Teacher many ---- many Subject (polymorphic pivot)
```

## 1. One-to-One

A one-to-one relationship connects one record to one related record.

`Profile` has one `Profile_detail`:

```php
// app/Models/Profile.php
public function profile_detail()
{
	return $this->hasOne(Profile_detail::class, 'profile_id', 'id');
}
```

The `profile_details` table stores the foreign key:

```php
$table->foreignId('profile_id')
	->constrained('profiles')
	->onDelete('cascade');
```

Usage:

```php
$detail = $profile->profile_detail;
$profile = $detail->profile; // Add belongsTo() if this reverse relation is needed.
```

## 2. One-to-Many

A one-to-many relationship connects one parent to many child records.

`Comment` has many `Like` records:

```php
// app/Models/Comment.php
public function likes()
{
	return $this->hasMany(Like::class, 'comment_id', 'id');
}
```

Usage:

```php
$likes = $comment->likes;
$likeCount = $comment->likes()->count();
```

The foreign key belongs on the many-side table, `likes.comment_id`. The
`onDelete('cascade')` rule removes a comment's likes when that comment is
deleted.

## 3. Many-to-Many

A many-to-many relationship connects many records on both sides through a
pivot table. The original student-subject implementation uses the standard
`student_subject` pivot:

```php
public function subjects()
{
	return $this->belongsToMany(
		Subject::class,
		'student_subject',
		'student_id',
		'subject_id'
	)->withPivot('marks');
}
```

The pivot table contains `student_id` and `subject_id`. Extra pivot data, such
as marks, is accessed with:

```php
$marks = $student->subjects->first()->pivot->marks;
```

The reverse relation on `Subject` uses `belongsToMany()` with the keys in the
opposite direction.

## 4. Polymorphic One-to-One

A polymorphic relationship lets one child table belong to more than one model.
The `profiles` table contains:

```text
profileable_id
profileable_type
```

`Student` and `Teacher` can both have one profile:

```php
// Student.php and Teacher.php
public function profile()
{
	return $this->morphOne(Profile::class, 'profileable');
}
```

`Profile` defines the inverse relation:

```php
public function profileable()
{
	return $this->morphTo();
}
```

Usage:

```php
$profile = $student->profile;
$owner = $profile->profileable; // Student or Teacher
```

The migration helper `$table->morphs('profileable')` creates the polymorphic
ID and type columns.

## 5. Polymorphic One-to-Many

Comments can belong to either a `Student` or a `Teacher`:

```php
// Student.php and Teacher.php
public function comments()
{
	return $this->morphMany(Comment::class, 'commentable')
		->orderBy('id', 'desc');
}
```

`Comment` defines the inverse:

```php
public function commentable()
{
	return $this->morphTo();
}
```

Usage:

```php
$studentComments = $student->comments;
$owner = $comment->commentable; // Student or Teacher
```

This avoids separate `student_id` and `teacher_id` columns in `comments`.

## 6. Polymorphic Many-to-Many

Both students and teachers can take or teach subjects. Instead of separate
pivot tables, the `courseables` table stores:

```text
subject_id
courseable_id
courseable_type
```

`Student` and `Teacher` use `morphToMany()`:

```php
public function subjects()
{
	return $this->morphToMany(Subject::class, 'courseable');
}
```

`Subject` uses `morphedByMany()` for each possible related model:

```php
public function students()
{
	return $this->morphedByMany(Student::class, 'courseable');
}

public function teachers()
{
	return $this->morphedByMany(Teacher::class, 'courseable');
}
```

Usage:

```php
$subjects = $student->subjects;
$students = $subject->students;
$teachers = $subject->teachers;
```

## 7. Has-Many-Through

`hasManyThrough()` retrieves records through an intermediate model. It is
useful when the application does not need to access every intermediate record
manually.

### Student to Profile Details through Profile

```php
public function detail()
{
	return $this->hasManyThrough(
		Profile_detail::class,
		Profile::class,
		'student_id',
		'profile_id',
		'id',
		'id'
	);
}
```

This method reflects the earlier conventional `student_id` design. The current
`Profile` migration is polymorphic, so this particular through relationship
only works when the database also has the expected `profiles.student_id`
foreign key and matching data. With the current polymorphic design, use
`$student->profile->profile_detail` instead, or add a dedicated polymorphic
through strategy.

### Student to Likes through Comments

```php
public function likes()
{
	return $this->hasManyThrough(
		Like::class,
		Comment::class,
		'student_id',
		'comment_id',
		'id',
		'id'
	);
}
```

This also reflects the earlier conventional `comments.student_id` design.
Because comments currently use `commentable_id` and `commentable_type`, the
polymorphic access path is the reliable current approach:

```php
$likes = $student->comments->flatMap->likes;
```

## Common Eloquent Operations

```php
// Eager load relationships and avoid repeated queries.
$students = Student::with(['profile', 'comments.likes', 'subjects'])->get();

// Create related records.
$student->comments()->create(['content' => 'A new comment']);
$comment->likes()->create(['name' => 'Alex']);

// Attach and detach many-to-many records.
$student->subjects()->attach($subjectId);
$student->subjects()->detach($subjectId);

// Check whether a relationship has related records.
$studentsWithComments = Student::has('comments')->get();
```

## Key Lessons

- `hasOne()` and `hasMany()` are used by the parent model.
- `belongsTo()` is used by the model containing the foreign key.
- `belongsToMany()` uses a normal pivot table.
- `morphOne()`, `morphMany()`, and `morphTo()` use an ID/type pair.
- `morphToMany()` and `morphedByMany()` share a polymorphic pivot table.
- `hasManyThrough()` reads through an intermediate model and depends on the
  expected foreign-key structure.
- Relationship names should describe the related data and are accessed as
  properties for loaded records or as methods when adding query constraints.

## Running the Project

```bash
php artisan migrate
php artisan serve
```

Install frontend dependencies and run Vite when frontend assets are needed:

```bash
npm install
npm run dev
```
