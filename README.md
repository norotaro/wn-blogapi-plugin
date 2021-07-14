# Blog API Plugin

A simple JSON Rest API for consume datas from [Winter Blog Plugin][winterBlogPlugin]

## Installation

Run the following command in a project's root directory:

```sh
composer require norotaro/wn-blogapi-plugin
php artisan winter:up
```

## Endpoints

The plugin provides endpoints for list posts, post details and list categories.

### List posts

Use the `api/norotaro/blogapi/posts` endpoint to get a list of latest blog posts. The endpoint accepts the following query parameters:

- **page** - this value is used to determine what page the user is on. The default value is 1.
- **category** - a category slug to filter the posts by. If not specified or has blank value, all posts are returned.
- **perPage** - how many posts to return on a single page (the pagination is supported automatically). The default value is 30.
- **sort** - the column name and direction used for the sort order of the posts. The default value is **published_at desc**.
- **exceptPost** - ignore a single post by its slug or unique ID. The ignored post will not be included in the list, useful for showing other/related posts.
- **exceptCategories** - ignore posts from a comma-separated list of categories, given by their unique slug. The ignored posts will not be included in the list.

The response is a JSON serialization of a Laravel paginator instance that also has the **category** atribute. This attribute represents the blog category object loaded from the database. If the category is not found or not specified, the attribute value is null.

The next example shows the basic endpoint usage using `curl`:

```sh
curl --request GET '{base_uri}/api/norotaro/blogapi/posts'
```

The next example shows the basic component usage with the category filter:

```sh
curl --request GET '{base_uri}/api/norotaro/blogapi/posts?category={category_slug}'
```

> In the examples you need to replace `{base_uri}` and `{category_slug}` strings with valid values.

Here is an example of the JSON returned:

```json
{
   "total": 50,
   "per_page": 15,
   "current_page": 1,
   "last_page": 4,
   "first_page_url": "http://winter.app?category=test&page=1",
   "last_page_url": "http://winter.app?category=test&page=4",
   "next_page_url": "http://winter.app?category=test&page=2",
   "prev_page_url": null,
   "path": "http://winter.app",
   "from": 1,
   "to": 15,
   "data":[
        {
            // Post Object
        },
        {
            // Post Object
        }
   ],
   "category": {
        // Category Object
    }
}
```

### Post detail

Use the `api/norotaro/blogapi/posts/{slug}` to get a blog post object.

The post object is returned with `categories`, `featured_images` and `content_images` relationships loaded.

The next example shows the basic endpoint usage with `curl`:

```sh
curl --request GET '{base_uri}/api/norotaro/blogapi/posts/{slug}'
```

If a post with the specified slug is not found, then the following response will be returned with a 404 status code:

```json
{
    "code": 404,
    "message": "Not found"
}
```

### List categories

Use the `api/norotaro/blogapi/categories` endpoint to get a list of blog post categories. The endpoint accepts the following query parameters:

- **displayEmpty** - determines if empty categories should be displayed. The default value is false.

The next example shows the basic endpoint usage with `curl`:

```sh
curl --request GET '{base_uri}/api/norotaro/blogapi/categories'
```

[winterBlogPlugin]: https://github.com/wintercms/wn-blog-plugin