#!/usr/bin/env python3
import os.path
import sys
import glob
import logging
import platform

from fabric import task

from fabfile.config import CONFIG


if __name__ == "__main__":
    print(
        '\033[0;31mThis file is not meant to be run on its own! Please use "fab" command.\033[0m'
    )
    sys.exit(-1)


# ! Logging

cl = logging.getLogger("console")


# ! Helpers


def setDefaultEnv(ctx):
    # Local commands need env variables
    ctx.config.run.replace_env = False
    ctx.config.run.env["DOCKER_SCAN_SUGGEST"] = "false"

    if platform.uname().machine == "arm64":
        ctx.config.run.env["DOCKER_BUILDKIT"] = "0"


# ! Run development environment


@task
def requirements(ctx):
    setDefaultEnv(ctx)


@task(pre=[requirements])
def install(ctx):
    setDefaultEnv(ctx)

    # Down docker
    # ctx.run("docker compose down --remove-orphans")
    # ctx.run("docker compose rm develop")

    # Build up
    ctx.run("docker-compose build develop")
    ctx.run("docker compose up --detach develop")


@task
def up(ctx):
    setDefaultEnv(ctx)
    ctx.run("docker compose up --detach develop")


@task
def down(ctx):
    setDefaultEnv(ctx)
    ctx.run("docker compose down")


@task
def uninstall(ctx):
    setDefaultEnv(ctx)
    ctx.run("docker compose down -v --rmi all --remove-orphans")


@task
def help(ctx):
    setDefaultEnv(ctx)

    cl.info(f"To run commands on docker: `sudo docker compose exec develop bash`.")
    cl.info(
        f"To view logs: `sudo docker compose logs develop`. Add -f before develop for monitoring."
    )


# ! Build deployable packages


@task(pre=[requirements])
def build(ctx):
    setDefaultEnv(ctx)

    # Build up
    ctx.run("docker-compose build build")
    ctx.run("docker compose up build")


@task
def cleanup(ctx):
    ctx.config.run.replace_env = False

    cl.info("Cleanup")
    ctx.run("docker compose run build /root/meta/scripts/cleanup.bash")


# ! Deployment


@task(pre=[build])
def deploy(ctx):
    cl.error("Not configured!")


@task(post=[cleanup])
def deploy_alone(ctx):
    deploy(ctx)


# ! Other


@task()
def dump_db_schema(ctx):
    setDefaultEnv(ctx)

    cl.info("Dumping DB schema .. ", extra={"end": ""})
    ctx.run(
        f"docker compose exec -u postgres -T pgdb pg_dump -w --schema-only --no-owner --no-privileges -f /tmp/data/db_schema.sql pm",
        hide=True,
    )
    ctx.run(
        f"mv {CONFIG['base_path']}/docker/pgdb/data/db_schema.sql {CONFIG['base_path']}/Application/Files/db_schema.sql",
        hide=True,
    )
    cl.info("Done", extra={"skip_addons": True})
