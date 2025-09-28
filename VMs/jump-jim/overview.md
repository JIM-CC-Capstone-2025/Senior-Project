# jump-jim - SSH/Telnet Honeypot

## Overview
Internal honeypot server designed to attract SSH and Telnet attacks following lateral movement from the compromised web server. Simulates telecommunications infrastructure management systems.

## VM Specifications

| Attribute | Value |
|-----------|-------|
| **VM Name** | jump-jim |
| **Instance Type** | t3.small |
| **RAM** | 2 GB |
| **Storage** | 32 GB |
| **Network** | Private Subnet (LAN) |
| **IP Address** | 10.0.2.30 |
| **External Access** | No |
| **OS** | Ubuntu 22.04 LTS |

## Purpose
- Serve as lateral movement target after web server compromise
- Capture SSH brute force attacks and session activities
- Provide Telnet service for legacy protocol attacks
- Monitor command execution and persistence attempts

## Key Services
- **SSH Server**: OpenSSH with weak authentication
- **Telnet Server**: Legacy protocol service
- **Filebeat**: Log forwarding to ELK stack on soc-jim
- **Fake Network Tools**: Simulated telecom management utilities

## Simulated Environment
- **User Accounts**: Multiple accounts with weak passwords
- **Fake Files**: Telecommunications configuration and network data
- **Network Discovery**: Responds to scanning with realistic internal network layout
- **Management Scripts**: Simulated telecom infrastructure management tools

## Security Posture
- **Intentional Vulnerabilities**: Weak SSH/Telnet credentials, privilege escalation opportunities
- **Session Recording**: Complete logging of attacker commands and activities
- **File System Monitoring**: Tracks file access and modifications
- **Network Activity**: Monitors outbound connections and scanning attempts

## Network Access
- **Inbound**: SSH (22), Telnet (23), limited internal access only
- **Outbound**: Log forwarding to soc-jim, limited internet access
