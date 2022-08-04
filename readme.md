# laravel-dynamic-dashboard

## A quick and easy way to set up a dashboard for a project

### Preamble

This package was developed using the TALL stack. This means Laravel, Livewire, Alpine and Tailwind will need to be installed.

This package works well together with the <code> the-riptide/laravel-dynamic-text </code> package, which lets you inline texts into the database from the front so that admin users can immediately edit it from the dashboard.

With this package, it becomes easy to set up most dashboards. There is no need to create dashboard views, controllers, models, or migrations. All you need to do is migrate once, then set up single file in a specific folder where you specify the model fields, what you'll show on the index, the relationships and function calls.

That done, the package will take care of the rest.

Note that this package is in Beta. Many features are still being developed. Tests have also not been set up.

Also, instead of only one model being called to (e.g. an Article model) this package instead uses a model per field (e.g. a string model) and links these together as a linked list.

This has the disadvantage that a lot more database calls will be made than usual. Though there is some caching which helps reduce this overhead, the package remains unsuitable for high-traffic projects. Instead, it's much more suitable to simpler websites where traffic will be in the hundreds per day rather than the thousands.

There are plans to reduce the database calls in the future so that the package will also work well with higher traffic sites.

### Installation

To install this package, use:

<code> php composer require the-riptide/laravel-dynamic-dashboard </code>

After that, you'll need to migrate. You can publish the files using:

<code> php artisan vendor:publish </code>

Then select this package from the list.

Because we're using livewire, you'll need to add the folder App\Livewire to make sure it doesn't throw an error. The folder can remain empty.

### Use

A folder has been created in the App folder called Dyndash. Inside, there is one example file, which shows the basic layout of the file.

To create a new dashboard item, you can use the artisan command 

<code>
php artisan dyndash:create YourName
</code>

This will create a dyndash file in the folder specified in the dyndash config file. The default is 'Dyndash' in your app folder. This file will contain three functions for you to populate. These are called:

* index
* fields
* relationships 

each of these needs to return a collection. In short, the 'index' function determines the columns on the dashboard index page. The 'fields' function determines the fields in your dyndash and 'relationships' determines which other Dyndash types this current type should have a relationship with. 

It is currently not yet possible to set up relationships with outside models. This functionality is forthcoming. 

### fields

The 'fields' method is where you specify the fields that will be used in YourNameHere. The key you provide will be the name of the field. In the subarray, all you need to specify is the 'type' of field you would like this field to be. So, if you want a text field, you should use 

<code> 'type' => 'text' </code>

That's all you need to do to get a working dashboard item.

So, for example:

<code>

    public function fields()
    {
        return collect([
        	'title' => [
                'type' => 'string'
            ],

            'image' => [
                'type' => 'image',
            ],

            'body' => [
                'type' => 'text'
            ],
        ]);
    }

</code>

Will generate a very simple article layout.

Currently, the available data types are:

-   Boolean
-   Date
-   Dropdown
-   Image
-   Integer
-   String
-   Text
-   Time

Each of these has an associated standard dashboard input field, validation rule, placeholder text, etc. Want to change any of these values? Open a sub array in the item called 'fields' and specify the new value there.

So, for example:

<code>

    'title' => [
        'type' => 'string',

        'attributes' => [
            'placeholder' => 'Enter your title here...',
        ],
    ],

</code>

You can set new 'placeholder' text, a new 'title' to show up on the dashboard, and new validation 'rules'.

Another options is to specify a new 'component'. This will allow you to create a custom component which will be called instead of that model's standard component. 

For the dropdown, you also need to provide an additional array with the dropdown items. in the 'attributes section, create a sub array called 'items'. In there, specify your dropdown items. The key will be the value assigned to the select item  while the content is what will be displayed as the dropdown text.

### index

In the index method, you specify which fields you'd like to show up on the index as columns. You can simply slot them in. If you need to show more than just the value in the field, add a sub array where you set 'function' to true.

Like so:

<code>

    public function index()
    {
        return collect([
            'title',
             'show' => [
                 'function' => true,
            ]
        ]);
    }

</code>

If you do this, then the package will look for a method inside the same file with the name you specified. 

So, in the example outlined above it would look for:

<code>
    public function show()
    {
        return 'Hello world!';
    }
</code>

Any field you've set will be available to this method. You can access them as you would any normal object's attribute (e.g. $this->title).

Note, these function can be used elsewhere as well.

### Relationships

You can connect any type with any type simply by creating an entry in the collection inside the Relationship function. The key needs to be the name of the Type that you're connecting the model to. This is already enough for that Relationship to then show up in the dashboard provided it has a field called 'head'. The head field will be shown in the dashboard. 

If there is no 'head' field on the type you've specified or you want to show a different value in the dropdown shown in the dashboard, create a sub array where you you specify the field you'd like to use with the key 'show'. 


<code>

    public function relationships() : Collection
    {
        return collect([
            'Event' => [
                'show' => 'name',

            ],
        ]);
    }

</code> 

To call all the related models on the front, use the following syntax:

<code>

    $events = $article->relationship('Event');

</code>


### Slugs

A slug is automatically generated based on the first field you've specified. So make certain that this is not a dropdown, date, time, file or an image! 

### Create new instances

The creation of new instances of the type via the index is automatically enabled. if you do not want this to be possible, set $canCreate to false in the type file. 

<code> protected $canCreate = false </code>

### Accessing the models on the Front

Currently you can 'find' and 'get' types. At one point we'll add a facade to enable the standard call as done in Laravel. 

For the moment, please use 

(new Type)->find($x);
(new Type)->get();

These will return an instance and a collection respectively. 

For the find, you can use either the slug or the id. 

first() functionality will be implemented soon. 

### Routes

The package sets up three standard routes:

'/dashboard/create/{type}'
'/dashboard/edit/{type}/{id}'
'/dashboard/{type}'

where 'type' is the name of your dashboard item.

### Changing fields in a Type

To update the fields in a type for existing model, first make the changes in the fields list and then run 

<code> php artisan dyndash:modify [TypeName] </code>

Currently, this command allows you to add a new field, remove an existing field, rearange the order of fields and change a field type.

To avoid errors, don't do several steps at once. Instead, make only one type of change at a time.

### Seeding and factories

Factories exist and can be run like so:

<code> $type = (new Type)->factory(); </code>

This will then generate an instance of that Type with data.

You can also feed data into a Type, for example in a seeder, like so:

<code> $type = (new Type)->create($data); </code>

This will then save the data into the type and it to the database. 

### The Order-Setter

Users can automatically reorder Types in the dashboard using the integrated order setter. This is implemented by default. 

If you can want it to not work for something, you can go to the Type and put the following like in the model file:

<code>protected $canOrder = false;</code>

Then it will use the 'updated_at' column instead.

Want to use a different column? set protected $order_by = [different_column] and that will be used instead.

### Admin permission
There are two ways to give a user admin permission. The first one is to go to you 'env' file and set their emaild to DASH_EMAIL='their@email'. At present, only one email can be set in this way. 

Alternatively, you can go to the dyndash config file and add their email to the 'emails'. 

However you do it, an account that is then registered with this email will then have access to the dashboard. 

Be careful! if you have extra emails floating around in here that are not registered, people could register using these emails and then access the dashboard. This is not what you want. 

This is a temporary solution. We're planning to improve this in future versions. 

### Dashboard logo
If you'd like to set your own logo in the dashboard, go into the config and in the 'application-mark' place the name and the route to the file in the public folder. It will then be shown in the dashboard.

### Extra entries in the dashboard menu
You can add new entries to the dashboard by adding them to the dyndash config file under the heading 'menu_items'. Once added, they'll automatically be included. The key will be the listed name. the 'route' should be set to the named route you'd like to use. 