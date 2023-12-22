currently outputs an html document containing a table populated with data from NWS api for downtown Troy, OH area

designed to be used with a cron job and run daily at 5 am (approximately) to build weather data for current day and next 6 days

`php weather.php "test=apples&x=arbitrary"`

the paramters are random and serve to prevent malicious users from requesting/executing the script at will
