# laravel-dynamic-dashboard

## A quick and easy way to set up a dashboard for a project

### Preamble

This package was developed using the TALL stack. This means Laravel, Livewire, Alpine and Tailwind will need to be installed.

This package works well together with the <code> the-riptide/laravel-dynamic-text </code> package, which lets you inline texts into the database from the front so that admin users can immediately edit it from the dashboard.

With this package, it becomes easy to set up most dashboards. There is no need to create dashboard views, controllers, models, or migrations. All you need to do is migrate once, then set up single file in a specific folder where you specify the fields and the index.

That done, the package will take care of the rest.

Note that this package is in Alpha. Many features are still being developed. Tests have also not been set up.

Also, instead of only one model being called to (e.g. an Article model) this package instead uses a model per field (e.g. a text model), which are then linked together as a linked list.

This has the disadvantage that a lot more database calls will be made than usual. As such, at current it unsuitable for high-traffic projects. Instead, it's much more suitable to simpler websites where traffic will be in the hundreds per day rather than the thousands.

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

To create a new dashboard item, create a new php file here and copy the code below:

<code>

namespace App\Dyndash;

    use TheRiptide\LaravelDynamicDashboard\Objects\DynamicBase;


    class YourNameHere extends DynamicBase 
    {

        public function index()
        {
            return collect([

            ]);
        }

        public function fields()
        {
            return collect([

            ]);
        }
    }

</code>

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

Each of these has an associated standard input field, validation rule, placeholder text, etc. Want to change any of these values? Open a sub array in the item called 'fields' and specify the new value there.

So, for example:

<code>

    'title' => [
        'type' => 'string',

        'attributes' => [
            'placeholder' => 'Enter your title here...',
        ],
    ],

</code>

You can set a new component to call, a new 'placeholder' text, a new 'title' to show up on the dashboard, and new validation 'rules'.

For the dropdown, you can also specify the 'items' that will show up in the dropdown in the attributes array. The key will be the value assigned to the select item  while the content is what will be displayed as the dropdown text.

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

### Slugs

A slug is automatically generated based on the first field you've specified. So make certain that this is not a file or an image! As otherwise you'll have some very weird slugs.

### Create new files

The creation of new items via the index is automatically enabled. if you do not want this to be possible, set $canCreate to false in the type file. 

<code> protected $canCreate = false </code>

### Accessing the models on the Front

If you want to access a specific model on the front, you need to call it like you would a normal model (though facades have not yet been enabled). So, say you have a type called 'Article'. You would go $article = New Article($identifier). Where the identifier is either the slug of the article or the id of the article. 

If you want to get all the articles, call the Dynamic Collection and specify what you're looking for (e.g. 'article'). Then call 'get' and it will return all these items.

<code> (New DynamicCollection('article'))->get(); </code>

There are plans to convert this to the standard Laravel syntax (e.g. Article::find() and Article::get()) but this is currently not yet enabled. 

### Routes

The package sets up three standard routes:

'/dashboard/create/{type}'
'/dashboard/edit/{type}/{id}'
'/dashboard/{type}'

where 'type' is the name of your dashboard item.

### The Order-Setter

Change the sort-order of items in a Model. It's automatically turned on. If you can want it to not work for something, you can go to the Type and put the following like in the model file:

<code>protected $canOrder = false;</code>

Then it will use the 'updated_at' column instead.

Want to use a different column? set protected $order_by = [different_column] and that will be used instead.

### Admin permission
To give a user access to the admin dashboard, add their email to their dyndash config file under 'emails'. Any account registered with this email will then have access to the dashboard. 

Be careful! if you have extra emails floating around in here that are not registered, people could register using these emails and then access the dashboard. This is not what you want. 

A good solution is to point to the env file and set an email in there. 

This is a temporary solution. We're planning to improve this in future versions. 

### Dashboard logo
If you'd like to set your own logo in the dashboard, go into the config and in the 'application-mark' place the name and the route to the file in the public folder. It will then be shown in the dashboard.

### Extra entries in the dashboard menu
You can add new entries to the dashboard by adding them to the dyndash config file under the heading 'menu_items'. Once added, they'll automatically be included. The key will be the listed name. the 'route' should be set to the named route you'd like to use. 