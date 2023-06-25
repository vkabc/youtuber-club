# Youtuber Club


## Quick start

Setup `php` and `composer` first. 
https://gist.github.com/vkabc/b0805966d8ef86677767670e5dd256dd


Add `Infuria Key`   to  `.env` 

```bash
#alternatively, you can setup postgres, easy quick start using sqlite
touch database/database.sqlite

#change the DB_* settings accordingly to which database you use, easiest is sqlite. DB_CONNECTION=sqlite and the rest DB_* empty value.
mv .env.example .env 

php artisan migrate

npm i

npm run dev

php artisan serve
```

## Tools Used
1) Hedera SDK
2) HashConnect

