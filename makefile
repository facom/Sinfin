DATABASE=Sinfin
USERDB=sinfin
TABLE="Reconocimientos"

clean:
	touch delete.pyc delete~
	rm -r *.pyc
	find . -name "*~" -exec rm {} \;

cleanrecon:
	@echo -n "Please provide password for user '$(USERDB)': "
	mysql -u sinfin -p $(DATABASE) -e "truncate table $(TABLE);"
	rm -rf data/recon/*

cleantrash:
	rm -rf trash/*

cleanall:clean cleanrecon cleantrash

commit:
	@echo "Commiting changes..."
	@-git commit -am "Commit"
	@git push origin master

pull:
	@echo "Pulling from repository..."
	@git reset --hard HEAD	
	@git pull
	@chown -R www-data.www-data .

backup:
	@echo "Backuping sinfin..."
	@bash -x backup.sh 

restore:
	@echo "Restoring table $TABLE..."
	@-p7zip -d etc/data/sinfin.tar.7z
	@-tar xf etc/data/sinfin.tar
	@echo -n "Enter root mysql password: "
	@mysql -u root -p Sinfin < etc/data/sinfin.sql
	@p7zip etc/data/sinfin.tar

permissions:
	@echo "Setting web permissions..."
	@chown -R www-data.www-data .
