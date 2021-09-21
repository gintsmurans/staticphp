#!/usr/bin/env python3
import logging

from invoke import Collection

import fabfile.console as console

import fabfile.docker as docker


# ! Logging

# Setup default logging
logger = logging.getLogger()
logger.addHandler(console.ConsoleHandler())
logger.setLevel(logging.ERROR)

# Setup our internal logging
cl = logging.getLogger('console')
cl.addHandler(console.ConsoleHandler())
cl.setLevel(logging.INFO)
cl.propagate = False


# ! Setup task collections

ns = Collection()

ns.add_collection(Collection.from_module(docker))
