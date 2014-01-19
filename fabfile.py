from __future__ import with_statement

import fabric
import os
from fabric.api import *
from fabric.colors import green, yellow, red
from ftplib import FTP
from git import *


config = {
  'beta': {
    'hosts'      : ['beta.children-eyes.at'],
    'git_repo'   : 'git@github.com:children-eyes/website.git',
    'local_repo' : '/beta'
  },
  'production': {
   'hosts'      : ['children-eyes.at'],
    'git_repo'   : 'git@github.com:children-eyes/website.git',
    'local_repo' : '/production'
  }
}
env.stages = config.keys()



###############################################################################
# SECTION: Constants
###############################################################################
DATABASE = {
   "local": {
      "userName": "user",
      "password": "password"
   }
}

FTP_ADDRESS = "ftp.something.com"
FTP_USER = "user"
FTP_PASSWORD = "password"
FTP_ROOT_DIR = "/appDirectory"

REPO_ROOT = "../"


###############################################################################
# SECTION: Private methods
###############################################################################
def _connectToFTP():
   print green("** Connecting to the server **")

   ftp = FTP(host=FTP_ADDRESS, user=FTP_USER, passwd=FTP_PASSWORD)
   return ftp

def _gitLatestFiles():
   print green("** Connecting to Git **")

   g = Git(REPO_ROOT)
   repo = Repo(REPO_ROOT)
   headCommit = repo.head.commit

   print "Head commit revision: %s" % headCommit
   print "Message: %s" % headCommit.message

   result = g.execute(["git", "diff-tree", "--no-commit-id", "--name-only", "-r", str(headCommit)])
   files = result.split("\n")

   return _filterForValidFiles(fileList=files)

def _filterForValidFiles(fileList):
   return [f for f in fileList if f.startswith(("components/", "www/"))]


###############################################################################
# SECTION: Actions
###############################################################################

def uploadLatest():
   print ""
   print green("** Upload latest changes **")

   ftp = _connectToFTP()
   files = _gitLatestFiles()

   for f in files:
      print yellow("Uploading file %s" % f)
      split = os.path.split(f)

      ftp.cwd(os.path.join(FTP_ROOT_DIR, split[0]))
      ftp.storlines("STOR %s" % split[1], open(os.path.join("../", f), "r"))

   ftp.quit()

