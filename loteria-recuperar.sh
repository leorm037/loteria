#!/bin/bash
for i in {2615..1}
do
   /usr/bin/php80 /home/storage/7/98/80/paginaemconstruc1/site_bolao/bin/console loteria:concurso:recuperar mega-sena -c $i
#   sleep 1
done
