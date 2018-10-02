Setup
====================

## How To Setup

### Dnsmasq

- Install and setup [Dnsmasq](https://passingcuriosity.com/2013/dnsmasq-dev-osx/)

**Docker Setup**

- Once that you have downloaded the repository, open that folder via terminal

- Execute that command in order to setup the docker-compose override (that can differ from user ot user)

    ```cp docker-compose.override.yml.dist docker-compose.override.yml```

- Now it's time to setup the env file

    ```cp .env.dist .env```

- Launch the Docker compose

    ```docker-compose up -d```

- [Download certs](https://drive.google.com/drive/folders/1V8lEB9koqBFZxS6THUAB2G5T_zQxSriI?usp=sharing) and move in that folder:

    ```config/docker/nginx-proxy/certs```

**Composer**

- Once that Docker Compose is Up, go in Web container:

    ```docker-compose exec web bash```

- And install composer!

    ```composer install```

**Setup the Database**

- Generate a new DB and update the schema with pre-compiled entities

    ```./bin/console doctrine:database:create```

    ```./bin/console doctrine:schema:update --force```

**Reload Docker**

- You need to restart Docker in order to make it work 

    ```docker-compose down && docker-compose up -d```
    
## How It Works

**POST**

- Post a new newsletter's subscriber `POST /newSubscriber` with a valid email address as `email` query parameter

    **Validation**
    - [**EmailValidatorController->emailFormatValidation()**] Check email format 
    - [**EmailValidatorController->emailRecordValidation()**] Check if already registered in DB
        - Return an specific message if the validation process return errors

    **User DB Record**
    - [**SubscriberDbManager->saveNewSubscriber()**] Save a new email address in DB with ``status = false (not confirmed)``
        - Return the Subscriber/Email entity and a temporany Token
        
    **Send a confirmation email**
    - [**EmailManager->sendConfirmationEmail()**] Send a confirmation email to the user with "special link" `temporany token + email address` that allow him to confirm the subscription 
    
    **Return**
    - If the process is completed without problems, return a JSON response with the email address and some info
    
**GET**

- When a user click on the link received by email `GET /confirmEmail` he will be redirected on this route than need `emailAddress` and `temporany token`:

    **Validation**
    - [**EmailValidatorController->emailFormatValidation()**] Check email format validation   
        - Return an specific error message if the validation process return errors    

    **User Status Switch**
    - [**SubscriberDbManager->confirmSubscriber()**] Check if the user exist in DB. If exist generate the `real token` and compare it with `given token`. If the tokens are equal, switch the `subscriber confirmation status` from false to true.
        - Return the current subscriber if the confirmation is switched to true
        - Return Exception if subscriber doesn't exist 
        - Return Excpetion if the real token is not the same of given token
        - Return Exception if the email status is already confirmed

**Entities**

- (A) Emails (subscribers)

### How To Test API

- Run this project by ```docker-compose up -d```

- Insert a real valid email in `tests/Controller/MainTest.php` (ROW 9) `const REALEMAIL`

- Run ``./vendor/bin/phpunit`` 

    `testSimulateNewSubscriberRequest()`
    - Generate a new subscriber in the DB
    - Send confirmation email 
    - Simulate a click on the confirmation email link
    - Change status (NotConfirmet -> Confirmed) 
    - Send another email where the user is informed that the confirmation process is completed
    
    `testSimulateNewSubscriberInvalidRequest()`
    - Use a invalid email to check if the validation process is working
    
    `testSimulateAlreadySubscribedRequest()`
    - Use the real email `self::REALEMAIL` to check if the user is already registered 
    
- *Remember to change the email at every test cycle*     
 