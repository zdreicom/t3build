<?php

namespace Deployer;

$pathToDeployer = '<DEPLOYER_PATH>';
$pathTorRecipeDeployer = '<DEPLOYER_RSYNC_PATH>';

require $pathToDeployer . '/recipe/common.php';
require $pathTorRecipeDeployer . '/recipe/rsync.php';

set('application', 'p1136-t3build');
set('webroot', 'web');
set('allow_anonymous_stats', false);

/**
 * Host
 */
host('production')
    ->hostname('<HOST>')
    ->user('<USER>')
    ->port(22)
    ->set('deploy_path', '<PROJECT_PATH>')
    ->set('rsync_src', '<SOURCE>');

//set('release_name', 'timo');


/**
 * Main task
 */
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'rsync',
    'deploy:shared',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
])->desc('Deploy your project');

/**
 * Shared directories
 */
set('shared_dirs', <SHARED_DIRS>);

/**
 * Shared files
 */
set('shared_files', <SHARED_FILES>);

/**
 * Writeable directories
 */
set('writable_dirs', <WRITABLE_DIRS>);

set('rsync',[
    'exclude' => [
        '.git',
        'deploy.php',
        '.Build'
    ],
    'exclude-file'  => false,
    'include'       => [],
    'include-file'  => false,
    'filter'        => [],
    'filter-file'   => false,
    'filter-perdir' => false,
    'flags'         => 'rzl',
    'options'       => ['delete', 'delete-after', 'force'], //Delete after successful transfer, delete even if deleted dir is not empty
    'timeout'       => 3600, //for those huge repos or crappy connection
]);

after('deploy', 'success');
after('deploy:failed', 'deploy:unlock');
