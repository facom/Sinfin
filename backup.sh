#!/bin/bash          
database="Sinfin"
backdir="data/dump"
datadir="data/recon data/movilidad"

echo "Dumping..."
mysqldump -u root -p $database > $backdir/$database.sql

echo "Compressing..."
tar cf $backdir/$database.tar $backdir/$database.sql $datadir
p7zip $backdir/$database.tar

echo "Splitting..."
cd $backdir
rm $database.tar.7z-*
split -b 1024k $database.tar.7z $database.tar.7z-
cd - &> /dev/null

echo "Git adding..."
git add --all -f $backdir/*.tar.7z-*
echo "Done."
