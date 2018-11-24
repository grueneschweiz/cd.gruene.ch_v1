#!/usr/bin/env bash

real-favicon generate faviconDescription.json faviconData.json webroot
real-favicon inject faviconData.json src/Template/Element src/Template/Element/favicon.ctp