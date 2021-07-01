<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', '<project_name>');

// Project repository
set('repository', 'git@github.com:<github repo name>.git');


// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', false);

// Shared files/dirs between deploys
add('shared_files', []);
add('shared_dirs', [
    'storage'
]);

set('keep_releases', 5);

// Writable dirs by web server
add('writable_dirs', [

]);

// Hosts
host('<domain-name>')
    ->user('<server_login_user>')
    ->stage('staging') //stage name
    ->set('env', [
        'DB_DATABASE' => '<database_name>',
        'DB_USERNAME' => '<database_login_user>',
        'DB_PASSWORD' => '<database_password>'
    ])
    ->identityFile('c:/Users/newbiew/.ssh/id_rsa')
    ->set('deploy_path', '/var/www/html/<directory_name>'); //must be same as define at nginx host

set('ssh_multiplexing', false);

set('composer_options', 'install --verbose');

// Tasks
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'artisan:storage:link',
    // 'artisan:view:cache',
    'artisan:config:cache',
    'deploy:symlink',
    'deploy:failed',
    'artisan:october',
    'reload:php-fpm',
    'cleanup'
]);

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
task('artisan:october', function () {
    run('{{bin/php}} {{release_path}}/artisan october:up');
});


task('reload:php-fpm', function () {
    run('sudo /usr/sbin/service php7.4-fpm reload');
});




