#!/bin/bash 

git filter-branch --env-filter '
if [ "$GIT_AUTHOR_EMAIL" = "djart@linux.gr" ];
then
export GIT_AUTHOR_EMAIL="djart@blackmajesty";
fi
if [ "$GIT_COMMITTER_EMAIL" = "djart@linux.gr" ];
then
export GIT_COMMITTER_EMAIL="djart@blackmajesty";
fi
' HEAD

# after the above, do a git push --force
