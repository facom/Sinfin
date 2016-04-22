#!/bin/bash          
database="Sinfin"
backdir="data/dump"
datadir="data/recon data/movilidad"

echo "Dumping..."
mysqldump -u root -p $database > $backdir/$database.sql
echo "Compressing..."
tar cf $backdir/$database.tar $backdir/$database.sql $datadir
p7zip $backdir/$database.tar
echo "Git adding..."
git add --all -f $backdir/*.tar.*
echo "Done."
