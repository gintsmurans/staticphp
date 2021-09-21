from os import environ, path

from dotenv import load_dotenv

# ! Config

basepath = path.abspath(path.dirname(__file__)+'/../')

env_filename = '.env'
load_dotenv(path.join(basepath, env_filename))


CONFIG = {
    'env_file': env_filename,
    'base_path': basepath,

    'servers': {
        'prod': {
            'hosts': environ.get('DEPLOY_PROD_HOSTS', '').split(','),
            'user': environ.get('DEPLOY_PROD_USERNAME'),
            'key_filename': environ.get('DEPLOY_PROD_KEY_FILENAME'),
            'key_passphrase': environ.get('DEPLOY_PROD_KEY_PASSPHRASE'),

            'local_script_path': environ.get('DEPLOY_PROD_LOCAL_SCRIPTS_PATH'),
            'remote_script_path': environ.get('DEPLOY_PROD_REMOTE_SCRIPTS_PATH'),

            'tmp_path': environ.get('DEPLOY_PROD_TMP_PATH'),
            'deploy_path': environ.get('DEPLOY_PROD_DEPLOY_PATH')
        },

        'test': {
            'hosts': environ.get('DEPLOY_TEST_HOSTS', '').split(','),
            'user': environ.get('DEPLOY_TEST_USERNAME'),
            'key_filename': environ.get('DEPLOY_TEST_KEY_FILENAME'),
            'key_passphrase': environ.get('DEPLOY_TEST_KEY_PASSPHRASE'),

            'local_script_path': environ.get('DEPLOY_TEST_LOCAL_SCRIPTS_PATH'),
            'remote_script_path': environ.get('DEPLOY_TEST_REMOTE_SCRIPTS_PATH'),

            'tmp_path': environ.get('DEPLOY_TEST_TMP_PATH'),
            'deploy_path': environ.get('DEPLOY_TEST_DEPLOY_PATH')
        },
    }
}


def getServerConfig(server):
    if not server in CONFIG['servers']:
        raise Exception("Missing configuration: %s" % server)

    return CONFIG['servers'][server]
