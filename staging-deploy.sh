#!/usr/bin/env bash

env=topditop-production
compose_file=docker-compose-staging.yml
project_name=topditop-staging
web_container=topditop-staging_web_1
branch=release/staging
remote_deploy_path=/home/deployer/apps/topditop-staging

source ./base-deploy.sh