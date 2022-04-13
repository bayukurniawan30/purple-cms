# Setup {docsify-ignore-all}

Setup Purple CMS in 3 steps, Database, Administrative, and Finising Setup.

#### Database

Fill the database, user, and password field for database connection. The database must be created first, with collation <code>utf8mb4_general_ci</code>.

#### Administrative

Fill the site name and your data to create your account. Please use valid email, so the sign in information can be sent to your email (Must be connected to internet).

#### Finishing Setup

Finish the setup by pressing the Start Purple Button. If you are connected to the internet, you will get the email with the Sign In data (Please check your inbox and spam folder). 

#### Setup Using CLI

To setup Purple CMS using CLI, use [Migrate and Seed command](migrate-seed-db.md). But before that, you need to save the database information, [Click Here](cli-database.md) to view the instruction. Then [Generare the secret key and production key](cli-key.md). After that, migrate and seed the database.