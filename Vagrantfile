# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure( 2 ) do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.hostname = "vagrant"
  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.network "private_network", ip: "192.168.33.13"

  config.vm.provider "virtualbox" do |vb|
    vb.memory = "2048"
  end

  config.vm.provision "ansible" do |ansible|
    ansible.playbook = "vagrant.yml"
    ansible.sudo = true
  end
end
