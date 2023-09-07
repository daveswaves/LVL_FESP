# TIPS

### Laravel GitHub projects should be easy to setup.
- Project should run off the bat, no tweeks required.
- Running ```$ php artisan migrate:fresh --seed``` should execute without errors.
- Instructions on using the application (username, password etc) should be made clear in the README.

### Use ```Route::view()``` if your route is returning a view.

```
Route::get('/', function () {
    return view('index');
});
```

Shorthand:

```
Route::view('/', 'index');
```

### Editors like PhpStorm provide commands like ```Reformat Code``` to apply correct spacing, indentation etc.

### Apply middleware() to routes not controllers - easier to see what's going on.

Eg.
```
Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [HomeController::class, 'insert'])->name('dashboard');
    Route::resource( etc.
});
```

### If you're using resource controllers ```php artisan make:controller PhotoController --resource```, your application may not use all the created methods:

```
public function index() {}

public function create() {}

public function store(Request $request) {}

public function show($id) {}

public function edit($id) {}

public function update(Request $request, $id) {}

public function destroy($id) {}
```

*** Remember to delete any unused methods ***

### It's safer to use ```$request->only()``` rather than ```$request->all()```

```
auth()->user()->update($request->only([
    'name',
    'email',
    'password',
    etc.
]));
```

### Use ```route('route_name')``` instead of ```url()``` Eg:
```
Route::post('ajax/insert/', [AjaxController::class, 'insert'])->name('ajax.insert');
```

*** Remember to add fields to the Model's ```protected $fillable = []``` ***

### Searching Laravel Docs

Goto [Laravel Docs](https://github.com/laravel/docs) on GitHub and enter your search term (eg. authserviceprovider) in the "Search or jump to..." box (top left).

There are 3 options ```In this repository``` ```In this organization``` ```All GitHub``` Choose the first.




### Laravel Debugbar

[laravel-debugbar](https://github.com/barryvdh/laravel-debugbar)

Install laravel-debugbar

```shell
$ cd to project
$ composer require barryvdh/laravel-debugbar --dev
```

Laravel Debugbar should start automatically. The following gets added to the `composer.json` file, so restart server:

```shell
"require-dev": {
    "barryvdh/laravel-debugbar": "^3.7",
    ...
```

To disable Debugbar add the following to the .env file: `DEBUGBAR_ENABLED=false`


---

### Hints / Tips

```
➤ Use clear docblock descriptions.
➤ Check code is clear of all debug/unnecessary comments (comments that just repeat method names are unnecessary). 
➤ Delete all unused files.
➤ Using private methods that only have one or two lines of code, or only get called once, are often preferable.
  They help to make code more readable.
```


<details>
<summary>NOTES</summary>

```
::create() uses eloquent and is slow. If you're inserting 100 records, it can only do it by running 100 separate queries.
::insert() can insert the 100 records in 1 query.

insert() doesn't use eloquent, so 'created_at' and 'updated_at' need to be added manually, if required:

'created_at' => now()->toDateTimeString(),
'updated_at' => now()->toDateTimeString(),


$chunks = array_chunk($data, 5000);
foreach ($chunks as $chunk) {
    Product::insert($chunk);
}

*** Check out eager loading and n+1 problem in Laravel Docs. ***

Install laravel-debugbar (set .env/APP_DEBUG=false to disable):
PROJECT_DIR$ composer require barryvdh/laravel-debugbar --dev
```
</details>
