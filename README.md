## Setup
This project has been built with laravel sail.

I have used pint for styling
I have used PHPStan for static analysis. There are a few outstanding issues but its set to level 9

## Commands
`sail artisan app:import-postcode` - Optional `--skip-download`.
This command will download a hardcoded zip file and create a job to extract. This job created more jobs within the batch to process each CSV file and inserts the data into the DB.
Using jobs allows for retry mechanisms and async processing

`sail artisan horizon`
Run the jobs to download and process the files

## API Authentication & Auth
I have used sanctum for this and created 2 users within a seeder
`sail artisan db:seed`
A user which is allowed to create stores can be used with the bearer token: 2d4b13702d1a6f35d4fed1b68641230d
A user that cannot create stores but can call GET endpoints can be used with bearer token: f5f132a18d409e4b8284307c4c481487

## API
POST /stores - Create a new store
GET /stores?postcode= - Get stores in order of distance
GET /stores/deliverable?postcode?= - Get stores that can deliver based on the distance

I opted to pass the postcode in as a parameter as this would allow the next logical progression of allowing lat,long if the FE used a map.
Having /deliverable makes the URL simple to understand but this too could have been a filter parameter.

I have added a postman collection. I have been unable to test this as I cannot sign in to postman on the desltop app (need to reinstall I assume)
Use as a guide.

## DB and issues
I had a lot of issues working with the Spatial data, this is the first time I have used it and it seemed to make the most sense. I included lat and long as well as coord because of these issues
but in the end I managed to get it working with coords

With more time I would clean this up but having the lat long does provide some value for outputting easier.

## Tests
I ran out of time to do any tests due to the issues I had with the Spatial fields within the DB but this is what I would do:
 - Feature test each HTTP endpoint
 - Feature test the command
 - Feature test each job
 - Unit testing will require a lot of mocking given most of the logic is interacting with files and database which would be solved with repositories
 - With repositories I would be able to unit test more of the code as these calls can be mocked

## Repositories
All DB queries should be within a repository. Repositories can be unique to the context. This means we may have several queries doing something similar but get data for a different purpose.
Keeping these separate prevents queries getting large as each new feature needs "just a little more" data.

## What else to do
- Repositories is a massive missing compontent to this solution. They are simple to create, allows for an interface to be used for swapping data sources. Also allows for testing easier.
- Tests clearly are missing. This is not acceptable for a production application. Tests give confidence you can deploy with little issue (we always hope for no issues! When a issue occurs a test can replicate it and the fix can then be proven)
- API GET: Allow the user to provide lat,long as if they have selected a point on a map
- API POST: Allow the store creator to save a Postcode. Use the postcode table to get the coords. This seems like an easy win.

## Thank you
Thank you for your time. I look forward to hearing your feedback. In the meantime, I will be actioning my own points because I am not happy to draw a final line on this...yet.