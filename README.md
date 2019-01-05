#Flannel Core Framework

## Overview
The Flannel Core Framework has been an internal project for years being primarily used to rapidly deploy websites and REST APIs without the overhead.

*Please note - we are currently in the process of making this a public framework, so please bear with us over the next few weeks as we pull everything together.*

## Keeping your codebase updated
1. <code>git remote add flannel git@github.com:flannel-dev-lab/flannel-framework.git</code>
2. <code>git fetch flannel</code>
3. <code>git checkout -b framework flannel/master</code>
4. <code>git checkout master</code>
5. <code>git merge framework</code>
6. <code>git add -A</code>
7. <code>git commit -m "Merging Flannel Core Framework"</code>
8. <code>git push origin master</code>

Now, when you need to pull the most recent version, you just need to run the following:
1. <code>git checkout framework</code>
2. <code>git pull framework master</code>
3. <code>git checkout master</code>
4. <code>git merge framework</code>
5. <code>git push origin master</code>
