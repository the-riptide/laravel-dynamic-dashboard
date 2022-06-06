# laravel-dynamic-dashboard

## A quick and easy way to set up a dashboard for a project

### Preamble

This package was developed using the TALL stack. This means Laravel, Livewire, Alpine and Tailwind will need to be installed.

With this package, it becomes easy to set up most dashboards. There is no need to create dashboard views, controllers, models, or migrations. All you need to do is set up one File and in there specify the fields of the dashboard and what fields are to be included in the index.

That done, the package will take care of the rest.

Note that this package is in Alpha. Many features are still being developed. Tests have also not been set up.

Also, instead of only one model being called to (e.g. an Article model) this package makes use of a series of models, with each field represented by one model.

This means that this dashboard is more demanding than other models, which makes it unsuitable for high-traffic projects (though that might change at a future date).

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

namespace app\Dyndash;

    class YourNameHere
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

The 'fields' function is where you specify the fields of your new entry. The key you provide will be the name of the field. In the subarray, all you need to specify is the 'type' of field you would like this field to be.

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

Each of these has an associated standard input field, validation rule, placeholder text, etc. Want to change any of these values? Open a sub array in the item called 'fields' and specify the new value there.

So, for example:

<code>

    'title' => [
        'type' => 'string',

        'fields' => [
            'placeholder' => 'Enter your title here...',
        ],
    ],

</code>

You can set a new component to call, a new 'placeholder' text, a new 'title' to show up on the dashboard, and new validation 'rules'.

For the dropdown, you can also specify the 'items' that will show up in the dropdown. The key will be the value assigned to the field while the content is what will be displayed in the dropdown.

### index

In the index method, you specify which fields you'd like to show up on the index. You can simply slot them in. If you need to show more than just the value in the field, add a sub array where you set 'function' to true.

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

If you do this, then the package will look for a function called get[your field]. In this case, it would look for getShow. The model associated with that field will be passed in as a parameter. You should return whatever you would like to show on the dashboard as a result of that value. You can access the value by calling the name of the field (in this case $post->show).

### Routes

The package sets up three standard routes:

'/dashboard/create/{type}'
'/dashboard/edit/{type}/{id}'
'/dashboard/{type}'

where 'type' is the name of your dashboard item.

### The Order-Setter

Change the sort-order of items in a Model. It's automatically turned. If you can want it to not work for something, you can go to the Type and put the following like in the model file:

<code>protected $canOrder = false;</code>

Then it will use the 'updated_at' column instead.

Want to use a different column? set protected $order_by = [different_column] and that will be used instead.
