dir=/media/sf_jzuluaga/Downloads
IFS=","
for file in $(ls -m $dir/*.csv)
do
    file=$(echo $file |tr '\n' ' ' |sed -e 's/[[:space:]]*$//')
    name=$(basename $file)
    base=$(echo $name |cut -f 1 -d ' ')
    cp $dir/"$name" planes/$base.csv
done
