# QueueBundle
Queue (producers + consumers) system for Symfony framework based on Pheanstalk

## Requirements

 - PHP >= 7.0
 - Symfony 3
 - [Beanstalkd workqueue](http://xph.us/software/beanstalkd/) installed locally
 
## Installation
### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the following command to download the latest version of this bundle:

``` bash
$ composer require mate/queue-bundle dev-master
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in the `app/AppKernel.php` file of your project:

``` php
<?php
// app/AppKernel.php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Mate\QueueBundle\MateQueueBundle(),
        );
    }
}
```

### Step 3: Add the overriding parameter

Add the your pheanstalk server host parameter to the `app/config/parameters.yml` file:

```yaml
# app/config/parameters.yml
mate_worker_host: 127.0.0.1
```
## Getting started
This bundle make it simple to create your own jobs/tasks that takes long time to executed. Let's imagine that we have an application that send confirmation mails to users.

### Create Job (ConfirmationMailJob.php)
Let's create a class called `ConfirmationMailJob.php` in `AppBundle\Job` namespace

```php

namespace AppBundle\Job;

use Mate\QueueBundle\Worker\Job;
use Symfony\Component\Security\Core\User\User;

class ConfirmationMailJob extends Job
{
    /** @var User */
    protected $user;

    /** @var \Swift_Mailer */
    protected $mailerService;

    /**
     * ConfirmationMailJob constructor.
     *
     * Dependencies:
     * 
     * @param User $user
     * @param \Swift_Mailer $mailerService
     */
    public function __construct( User $user, \Swift_Mailer $mailerService )
    {
       $this->user          = $user;
       $this->mailerService = $mailerService;
    }


    /**
     * Method used to execute this job
     * by our Queue worker later
     */
    public function handle()
    {
        //
    }
}
```
Our ConfirmationMailJob class should extend the Job (abstract) class of this bundle, and it require the **handle()** method.

Now lets write some basic code to send mail to the authenticated user in our **handle()** method.

```php
/**
     * Method used to execute this job
     * by our Queue worker later
     */
    public function handle()
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('send@example.com')
            ->setTo($this->user->getEmail())
            ->setBody('Confirmation mail here', 'text/plain')
        ;

        $this->mailerService->send($message);
    }
```
That's it :), the last thing we should do here is to call the Producer service in our controller and pass this job to its the `produce()` method

```php
/**
     * @param Producer $producer
     * @param \Swift_Mailer $mailerService
     *
     * @return Response
     *
     * @Route("/", name="homepage")
     * @Security("has_role('ROLE_USER')")
     *
     * @throws \Exception
     */
    public function indexAction( Producer $producer, \Swift_Mailer $mailerService ): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Produce the job to our Pheanstalk server
        // And injecting the desired services
        $producer->produce(new ConfirmationMailJob($user, $mailerService), 3);

        return $this->render('default/index.html.twig');
    }
```
That's it :), you're done.
Please note that the `produce(Job $job, $delay = 0, $timeToRun = 60)` method accept these parameters:

 1. Job class
 2. Delay (seconds): by default 0 - no delay
 3. Time to run (seconds): by default 60

### Run the queue worker
The last thing we should do is to listen to the executed jobs on our application, to do this you have to run the command
```bash
$ php bin/console mate:queue:work
```

Now try to run your application server, and navigate to http://localhost:8000/ , take a look to your console:

```bash
[2017-10-08 23:31:30] Processing: AppBundle\Job\ConfirmationMailJob
[2017-10-08 23:31:30] Processed : AppBundle\Job\ConfirmationMailJob
```

Make sure to find some tools like Supervisor to automatically restart your `mate:queue:work` process if it fails.

## Advanced topics

### Queue Events
This package provide some events to help you manage your jobs (`Mate\QueueBundle\Event\JobEvent`):

 - **MATE_QUEUE_JOB_INITIALIZED** (onInitialized)
 - **MATE_QUEUE_JOB_EXECUTED** (onExecuted)
 - **MATE_QUEUE_JOB_FAILED** (onFailed)
 - **MATE_QUEUE_JOB_DELETED** (onDeleted)

// TODO

