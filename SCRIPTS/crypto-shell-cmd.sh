#!/bin/sh
curl https://www.worldcoinindex.com/ | sed -f coin-scrap.sed | sed -f sed-clean.sed | grep Ticker | more
