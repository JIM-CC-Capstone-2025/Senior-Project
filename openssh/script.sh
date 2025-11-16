#!bin/sh

wget http://snapshot.debian.org/archive/debian/20180801T000000Z/pool/main/o/openssh/openssh-server_7.4p1-10+deb9u3_amd64.deb
wget http://snapshot.debian.org/archive/debian/20180801T000000Z/pool/main/o/openssh/openssh-sftp-server_7.4p1-10+deb9u3_amd64.deb
wget wget http://snapshot.debian.org/archive/debian/20180801T000000Z/pool/main/o/openssh/openssh-client_7.4p1-10+deb9u3_amd64.deb

# Install vulnerable version
dpkg -i openssh-sftp-server_7.4p1-10+deb9u3_amd64.deb openssh-server_7.4p1-10+deb9u3_amd64.deb openssh-client_7.4p1-10+deb9u3_amd64.deb

# Prevent updates
apt-mark hold openssh-server openssh-sftp-server openssh-client

systemctl restart sshd
