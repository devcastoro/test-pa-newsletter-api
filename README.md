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

- And install the composer!

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

- Post a new newsletter subscriber `POST /newSubscriber` with a valid email address as `email` query parameter

    **Validation**
    - [**EmailValidatorController->emailFormatValidation()**] Check email format validation
    - [**EmailValidatorController->emailRecordValidation()**] Check if the email address is already registered)
        - Return an specific error message if the validation process return errors

    **User DB Record**
    - [**SubscriberDbManager->saveNewSubscriber()**] Save a new email address in DB with ``status (confirmation) = false``
        - Return the Subscriber/Email entity and a temporany Token
        
    **Send a confirmation email**
    - [**EmailManager->sendConfirmationEmail()**] Send a confirmation email to the user with "special link" `temporany token` that allow him to confirm his subscription 
    
    **Return**
    - If the process is completed without problems, return a JSON response with the email address and some info on the current subscriber status (not confirmed by default)
    
**GET**

- When a user click on the link received by email `GET /confirmEmail` he will be redirected on this route than need ``emailAddress`` and `temporany token``:

    **Validation**
    - [**EmailValidatorController->emailFormatValidation()**] Check email format validation   
        - Return an specific error message if the validation process return errors    

    **User Status Switch**
    - [**SubscriberDbManager->confirmSubscriber()**] Check if the user exist in DB. If exist generate the real token and compare it with given token. If the token is the same, switch the confirmation status from false to true.
        - Return the current subscriber if the confirmation is switched to true
        - Return Exception if subscriber doesn't exist 
        - Return Excpetion if the real token is not the same of given token
        - Return Exception if the email status is already confirmed

**Entities**

- (A) Emails (subscribers)

### How To Test API

- Run this project by ```docker-compose up -d```

- Run ``./vendor/bin/phpunit``
