# -*- mode: ruby -*-
# vi: set ft=ruby :
Vagrant.require_version ">= 1.8.0"

Vagrant.configure("2") do |config|

  config.vm.box = "debian/jessie64"

  config.ssh.insert_key = false
  config.vm.box_check_update = false
  config.vm.network "private_network", type: "dhcp"

  config.vm.synced_folder ".", "/vagrant", :disabled => true
  config.vm.synced_folder ".", "/home/vagrant/app", :owner=> "vagrant", :mount_options => ["dmode=775", "fmode=775"]

  config.vm.provider "virtualbox" do |vb|
     vb.gui = false
     vb.memory = "1024"
     vb.cpus = 1
  end

  config.vm.provision "shell", inline: <<-SHELL
     apt-get update

     # install and configure postgresql
     apt-get install -y postgresql
     sudo -u postgres psql -c "CREATE USER vagrant WITH SUPERUSER CREATEDB ENCRYPTED PASSWORD 'vagrant'"
     service postgresql restart

     # install rabbitmq
     apt-get install -y rabbitmq-server

     # install php 7.2 repo
     apt-get install -y apt-transport-https lsb-release ca-certificates
     wget -q -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
     echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list

     apt-get update

     # install php 7.2 and required exts
     apt-get install -y php7.2
     apt-get install -y php7.2-bcmath php7.2-mbstring php7.2-pgsql php7.2-xml php7.2-zip
     apt-get install -y git-core

     # install vendors
     cd /home/vagrant/app && sudo -u vagrant ./composer install --no-progress

     # create db and load fixtures
     sudo -u vagrant php /home/vagrant/app/console doctrine:database:create
     sudo -u vagrant php /home/vagrant/app/console doctrine:migrations:migrate -n
     sudo -u vagrant php /home/vagrant/app/console doctrine:fixtures:load -n
  SHELL
end
