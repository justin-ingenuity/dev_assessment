#### Setup .env.local
```
DATABASE_URL=
MAILER_DSN=
SENDER_EMAIL=
ADMIN_EMAIL=
```

### Run on project folder
```sh
$ php bin/console doctrine:migrations:migrate
$ symfony server:start
```
