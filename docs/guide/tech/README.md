# Technical documentation

## Requirements

We recommend you have a completely new Linux-based VPS (Virtual Private Server). This way practically no other web applications are running on this server, meaning no conflicts, less risk to take, more secure and up-to-date O/S at startup.

* VPS server (depending on expected load, minimum 2 CPU, 8 GB RAM recommended)
* Ubuntu (latest version)
* Docker (minimum 17.12.0+ Docker Engine release)
* Subdomain address (e.g. covidtest.institute.gov)
* SMTP mail server (being a VPS, it's considered an external service provider in configuration)

*HINT: For security reasons, you may want to configure the database server on a separate VPS. Close all unused ports and open the database connection only to the application's VPS. In this case, `db` section and all its references must be removed from the `docker-compose.production.yml` file. Settings for the database connection must be defined in .env*

## Operating specifications

### Basic API specs

We have separate backend and frontend repositories. Communication between them takes place via API. Important to note that connection should be made via encrypted `https`. The frontend must be built separately, wording and logos must all be replaced there. After building frontend, its `public` folder content must be copied to the `public` folder of the backend.

### Data encryption method

Since personal data as social security number (subject to special legal data handling requirements) are collected, it is vital to protect / encrypt the data stored in your database.
Data encryption is implemented and available, the complexity of the encryption can be adjusted, but it can no longer be modified after the first user registration. Encryption key and initialization vector must be defined in the environment variables. If these values are subsequently modified, all the previously stored user data will be permanently unrecoverable. That's way it is advisable to store them properly until the end of registration process or until the data is deleted (considering the provisions of the data management policy).

### Cronjob

After registration emails are getting sent out with a delay, in order to mitigate the load on the server. When sending a notification, two `.pdf` files are generated with the applicant's data. These files are being forwarded as email attachment to user who completed the registration. In high volume this can put a heavy load on the server, thus by default it is recommended to send `20` emails per minute.

The system is set to disable dates that can no longer be used. This is being checked by a script each minute.

## Installation

You may want to update your server with `apt-get update && apt-get upgrade`. At this point you will need to set up your host OS for security, such as a firewall, fail2ban and WAF. Next, you will need to install Docker CE (Community Edition).

### Docker CE installation

You can install the Docker by running the commands below.

```
# apt install apt-transport-https ca-certificates curl software-properties-common
# curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
# add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu focal stable"
# apt update
# apt install docker-ce
# apt install docker-compose
```

#### Running without root privileges (Optional)

For security reasons, it is better if you do not run Docker as administrator.

```
# usermod -aG docker ${USER}
# su - ${USER}
# usermod -aG docker username
```

### Environment variables

Our software uses environment variables located in `.env` file. You can find a sample (`.env.example`) that contains all the keys for the variables, and you will need to assign values to them.

|Key|Example value|Possible values|Explanation|
|-----|-----------|------------------|------|
|NODE_ENV|production|production, development|Defines runtime environment|
|DB_DRIVER|Mysqli|[Learn more](https://docs.laminas.dev/tutorials/db-adapter/)|Database adapter|
|DB_HOSTNAME|127.0.0.1|-|Database address|
|DB_PORT|3306|-|Database port|
|DB_DATABASE|your-database-name|-|Database name|
|DB_USER|your-username|-|Database user name|
|DB_PASSWORD|your-password|-|Database password|
|DB_CHARSET|utf8mb4|-|Database character set|
|MYSQL_RANDOM_ROOT_PASSWORD|1|0, 1|Random root password true/false for MySQL server|
|MYSQL_DATABASE|your-database-name|-|Database name on MySQL server|
|MYSQL_USER|your-username|-|User name on MySQL server|
|MYSQL_PASSWORD|your-password|-|Password on MySQL server|
|MYSQL_ALLOW_EMPTY_PASSWORD|0|0, 1|MySQL setting, connection without password (local)|
|JWT_ISS|http://localhost|-|JWT token identifier "issuer" |
|JWT_AUD|http://localhost|-|JWT token identifier "audience"|
|JWT_JTI|RdzPkZ9pucdV5JJw|-|JWT token unique identifier (randomly generated)|
|JWT_NBF|0|-|increase time between JWT token issuance and expiry|
|JWT_EXP|1|-|JWT token expiration|
|JWT_SECRET|vFG8fqDbNEffk3qr|-|JWT token encryption key (unique, randomly generated)|
|XDEBUG_MODE|off|off, develop, coverage, debug, gcstats, profile, trace|xdebug for development, more info: https://xdebug.org/docs/all_settings#mode|
|XDEBUG_HOST|host.docker.internal|-|xdebug host|
|XDEBUG_PORT|9001|-|xdebug listen port|
|SMTP_HOST|127.0.0.1|-|Mail server host address|
|SMTP_NAME|localhost|-|Mail server domain|
|SMTP_PORT|25|-|Mail server port|
|SMTP_CONNECTION_CLASS|plain|plain, login, crammd5|Mail server account authentication type|
|SMTP_CONNECTION_CONFIG_USERNAME|username|-|Mail server account authentication user name|
|SMTP_CONNECTION_CONFIG_PASSWORD|password|-|Mail server account authentication password|
|SMTP_CONNECTION_CONFIG_SSL|(empty), ssl, tls|-|Mail server account authentication password|
|SMTP_DEFAULTS_ADD_FROM|noreply@institute.gov|-|Outbound email sender|
|SMTP_DEFAULTS_ADD_FROM_NAME|Local government|-|Outbound email, sender name|
|SMTP_HEADERS_MESSAGE_ID_DOMAIN|institute.gov|-|Outbound email message id domain|
|ENCRYPT_SHA_TYPE|sha256|-|Encryption Secure Hash Algorithm type for data stored in DB|
|ENCRYPT_ENCRYPT_METHOD|AES-256-CBC|[Learn more](https://www.php.net/manual/en/function.openssl-get-cipher-methods.php)|Cipher methods for data stored in DB|
|ENCRYPT_SECRET_KEY|vhEJt8V732mvxg7MJQGETSD9k3pxQka5HY78EZ7Sve7p|-|Encryption secret key for data stored in DB (44 chars long)|
|ENCRYPT_SECRET_IV|W654sStDcEsNcGbVCdkdr|-|Encryption initialization vector for data stored in DB (21 chars long)|
|APP_PHASE|1|-|Phase allowed (<= 1)|
|APP_MUNICIPALITY|Local government|-|Name of the local government|
|APP_EMAIL|support@institute.gov|-|Local government (customer support) email address|
|APP_PHONE|"06-1-000-0001"|-|Local government (customer support) phone contact|
|APP_URL|"https://covidtest.institute.gov"|-|Web application URL|
|APP_URL_ADMIN|"https://covidtest.institute.gov/admin"|-|Web application admin page URL|
|APP_COMPANY_NAME_PART_1|"SAMPLE AMBULANCE"|-|The first line on the generated PDF|
|APP_COMPANY_NAME_PART_2|"xy Ltd."|-|The second line on the generated PDF|
|APP_COMPANY_FULL_INFO|"Sample Ambulance Ltd, address: 1111 Budapest, Alma street 0.; VAT number: 00000000000., company registration number: 00 00 000000; email: info@sample.hu"|-|More company info on PDF (privacy section)|
|APP_NOTIFICATION_FREQUENCY|20|-|Emails are getting sent with a delay, frequency of email / minute can be set here|
|APP_NOTIFICATION_MAIL_TESTTO|"test@institute.gov"|-|Email address for testing purpose|
|APP_NOTIFICATION_MAIL_REPLAYTO|support@institute.gov|-|'replay to' address|
|APP_APPOINTMENT_EXPIRED_TIME_DAY_IS_PLUS|1|0, 1|The bookable time is the next day|
|APP_APPOINTMENT_EXPIRED_TIME_HOUR|7|UTC+1 (0-24)|Expiration date (hours)|
|APP_APPOINTMENT_EXPIRED_TIME_MIN|0|UTC+1 (0-59)|Expiration date (minutes)|
|APP_ICS_NAME|"Free Covid test"|-|Name of event in calendar file attached in email|
|APP_ICS_DESCRIPTION|"Free Covid test details"|-|Description of event in calendar file attached in email|
|APP_SURVEY_DISABLE|1|0, 1|Survey email on/off|
|APP_SURVEY_TEMPLATE|"email/survey"|-|Survey template|
|APP_SURVEY_TIME|"18:00"|-|Survey email timer (by server time)|
|APP_SURVEY_URL|"https://forms.office.com/Pages/ResponsePage.aspx?id="|-|URL in survey email text|
|APP_SURVEY_MAIL_TESTTO|"test-survey@institute.gov"|-|Survey email address for testing purpose|
|APP_SURVEY_MAIL_SUBJECT|"Please give us a feedback"|-|Subject of survey email|
|APP_SURVEY_MAIL_REPLAYTO|support@institute.gov|-|'reply to' address for survey|
|RECAPTCHA_SECRET|{Google generated token}|-|Google ReCaptcha v3 secret key|

### Starting Docker-compose

Docker can run multiple containers simultaneously, they can be organized in units, service dependencies can be managed separately. This is controlled by `docker-compose.production.yml` file. For example, if you do not need a database because it is running on a separate server, you have to delete `db` service and its references. If there is not SMTP at your disposal or it cannot be used due to policy issues, you can add it here and it will be available after startup.

*Hint: If outbound mails are managed by an SMTP server installed on the new VPS and a mail server is already in use, a TXT record 'SPT' must be included in the DNS to prevent emails ending up in SPAM.*

`expose` and` ports` attributes are defined in the above-mentioned file. Both are port connections, but while `expose` only listens on internal network, `ports` release them on all network interfaces. This is something to keep in mind and handle with consideration.

After Docker image building, start the application service dependencies with the command below:

```
docker-compose -f docker-compose.production.yml up --build -d
```

You can stream log files to the console with the command below:

```
docker-compose logs -f
```

*HINT: When you start docker-compose, script file `setup.sh` will run, this may take a while. When it's complete (and it has run successfully), you should see a log print `INFO exited: setup (exit status 0; expected )` In case of a runtime error, make sure that EOL `LF` setting is applied with `setup.sh` (Unix standard), `CR + LF` setting can prevent the script from running*


#### ReCaptcha

Form page is protected against bots with Google reCaptcha v3. Key can be generated here [Google](https://www.google.com/recaptcha/admin/create). ReCaptcha is a must, however in case you have no intention to use it, line `\Middlewares\Recaptcha::class` must be removed from `config/routes.php`. Additionally, on frontend `SITE_KEY=` key must exist in respective `.env` file, value is allowed to be empty if ReCaptcha is not implemented.

#### Building frontend

As described in test-booking-frontend repository documentation, after having it built, the content of `public` folder must be copied to the location of this (test-booking) repository's `public\` folder. After this step the server will be able to render the frontend web application.

#### Creating Admin user

Use the following command to create the admin user:

```
docker exec test_booking_demo_webapp php bin/create-admin.php -f Firstname -l Lastname -e {email} -p {password} -r {role}
```

#### Authorization Levels (ACL - Role)

The system has permission levels. Latest version is available here: [config/autoload/authorization.global.php](config/autoload/authorization.global.php).

|Name            |Code     |
|----------------|---------|
|Guest           |guest    |
|Voluntary       |voluntary|
|Customer service|cs       |
|Admin           |admin    |
|Developer       |developer|

#### Creating locations

Coming soon ...

#### Generating dates

Coming soon ...

### Testing

#### Testing of your mail server

This is script is for testing outgoing mails.

```
docker exec test_booking_demo_webapp php bin/test/send-email.php
```

#### Testing PDF Generation

If the emails are successfully sent, you will also need to test pdf generation.

```
docker exec test_booking_demo_webapp php bin/test/create-pdf.php
```

### Certificate and https connection

An encrypted connection is essential to go live. If you do not have a valid certificate purchased, you can generate one using Let's Encrypt.

```
docker run -it --rm --name certbot -v "/etc/letsencrypt:/etc/letsencrypt" -v "/var/lib/letsencrypt:/var/lib/letsencrypt" certbot/certbot certonly -d {your_domain} --standalone --preferred-challenges http -m {your_admin_email}
```
