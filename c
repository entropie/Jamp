[DEFAULT]
verbose = True

[project]
target = hg:target
start-revision = INITIAL
root-directory = /home/mit/Tmp/S
state-file = tailor.state
source = svn:source
subdir = .

[hg:target]
repository = file:///home/mit/Source/Jamp

[svn:source]
module = /
repository = file:///home/svn/Jamp/

