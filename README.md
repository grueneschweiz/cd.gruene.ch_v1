# Corporate Design Images Creator
This tools aims to provide a simple way to generate images conforming the corporate design rules.
It's designed to be so simple to use, that no any further instructions are needed and no corporate
design rules can be broken.
See it in action ▶️ [cd.gruene.ch](https://cd.gruene.ch) (To get a login, ask the secretary of your
 canton).

## Why?
Not everybody has the software and skills to create corporate design images on his own. And it's not
everybodys hobby to learn all the rules of the corporate design.

## Contributing ...
... is cool, simple and helps to make the 🌍 a better place 🤩
1. Install [docker](https://docker.com). (If you're on a linux machine, make sure to follow the 
[post installation steps](https://docs.docker.com/install/linux/linux-postinstall/).)
1. Start docker
1. Clone this repo `git clone https://github.com/grueneschweiz/weblingservice.git`
1. `cd` into the folder containing the repo
1. Execute `bash scripts/install.sh` and have a ☕️ while 
it installs. 
1. Execute `docker-compose up -d` to start up the stack.
1. After a few seconds: Visit [localhost:8000](http://localhost:8000). If you
get a connection error, wait 30 seconds then try again.
1. Login using `admin@admin.admin` as email and `Admin2018` as password.

### The Stack
Using a Lamp stack on docker, the tool is built with [CakePHP](https://cakephp.org/). The image
processing is done with [ImageMagick](http://php.net/manual/en/book.imagick.php) and the frontend
uses a bunch of JS-Tools bundled by [Webpack](https://webpack.js.org/). Have a look at the `package.json`
if you want to dig deeper.

### Where to begin
Grab an issue and get started

### Things you probably need to know
- As the font used in the corporate design is proprietary, you'll need to get a licenced copy. 
Contact us, for more info.
- The `JS` and `CSS` source files are located in `src/Assets` and then transpiled into the `webroot`.
- Since the project started a long time ago, the `JS` is still in ECMAScript 5.
- Please update the MySQL workbench model in `src/Model/Workbench` and the `.docker/seed.sql`, if you 
make changes to the database schema. 

### Tooling
#### Docker Cheat Sheet
- Start up: `docker-compose up -d`
- Shut down: `docker-compose down`
- Execute CakePHP CLI commands (enter container): `docker exec -it cd_app bash`. 
Use `exit` to escape the container.
- Add dependency using composer: `docker-compose -f docker-compose.install.yml 
run composer composer require DEPENDENCY` (yes, `composer composer` is correct,
the first one defines the container to start the second one is the command to
execute)
- Add dependency from npm: `docker-compose -f docker-compose.install.yml 
run node npm --install DEPENDENCY` (You may want to use `--save` or `--save-dev` as
well. Check out the [Docs](https://docs.npmjs.com/cli/install).)

#### Mailhog
All mail you send out of the application will be caught by [Mailhog](http://localhost:8020)

#### MySQL
Use the handy [phpMyAdmin](http://localhost:8010) or access the mysql CLI using
`docker exec -it cd_mysql mysql --user=cake --password=cake cake` 

#### NPM
Access the watching container using `docker exec -it cd_node bash` or use 
`docker attach cd_node` to get the output directly on your console.
