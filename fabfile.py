from __future__ import with_statement

import fabric
from fabric.api import *
from fabric.colors import green, yellow, red
from ftplib import FTP
from git import *
from fabric.operations import local as run_local
import os as py_os

run_ssh = run
os_env  = py_os.environ

config = {
  'default': {
    'git': {
      'repo'   : 'git@github.com:children-eyes/website.git',
      'user'   : '',
      'pass'   : '',
      'key'    : ''
    },
    'ftp': {
      'host'   : '',
      'user'   : '',
      'pass'   : '',
      'path' : ''
    }
  },

  'local': {
    'host'      : '127.0.0.1',
  },
  'beta': {
    'hosts'      : ['beta.children-eyes.at'],
    'path'       : '/beta',

  },
  'production': {
    'hosts'      : ['children-eyes.at'],
    'path'       : '/production'
  }
}

#TODO merge config default with concrete stages
env.stages = config.keys()


#stages
#TODO generate stage methods 'local', 'beta', 'deploy' ans set env.stage if called
  #def local(): _set_stage_config('local')
#actions
def deploy():
  _enshure_stage_isset()
  _check_not_stage('local')

  #TODO git checkout/update to local shared folder
  #TODO sync with ftp

def db(command):
  if 'export' in command:
    run('...')
  elif 'import' in command:
    run('...')


#helper
def _enshure_stage_isset():
  if not env.stage: raise "select stage first ("+_get_stages()+")"

def _set_stage_config(stage):
  os_env['ENVIRONMENT'] = stage #helps for testting
  env.stage = stage
  env.hosts = config[stage]['hosts']
  env.config = config[stage]
  _set_run_method()


def _set_run_method():
  global run
  if env.hosts[0] is 'localhost' or env.hosts[0] is '127.0.0.1':
    run = run_local
  else:
    run = run_ssh


def _check_not_stage(stage_name):
  if env.stage is stage_name: raise Exception('you dont want to do this on stage "'+stage_name+'" ;)')


def _check_stage(stage_name):
  if not env.stage is stage_name: raise Exception('you only want to do this on stage "'+stage_name+'" ;)')
