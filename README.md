<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Twitter Api</h1>
    
    
</p>

CONFIGURATION
-------------

### Database

Edit the file `config/db.php` with real data, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=46.4.174.205;dbname='yii2advanced',
    'username' => 'yii2advanced',
    'password' => 'yii2advanced',
    'charset' => 'utf8',
];
```

Examples
-------
Since Twitter provided OAuth 2.0 Bearer Token, we have possibility to use
only Access Bearer Token for tweets and users search.https://developer.twitter.com/en/docs/basics/authentication/oauth-2-0/bearer-tokens




- `GET` /api/web/v1/token/new
Generates new Bearer Token and save it to database table `token`

- `GET` /api/web/v1/twitter/add?user_name=tproger
Adding twitter user with name `tproger` to our feed list (stored in database table `twitter`

- `GET` /api/web/v1/twitter/remove?user_name=tproger
Deleting twitter user with name `tproger` from our feed list and database

- `GET` /api/web/v1/twitter/feed
Gets latest tweets from oll feeds list users
If user have protected account -> returns message that account not available


