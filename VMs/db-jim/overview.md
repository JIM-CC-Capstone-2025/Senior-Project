# db-jim - Database Server

## Overview
Backend database server storing realistic telecommunications customer data, billing records, and operational information. Designed to be a target for lateral movement and data exfiltration attempts.

## VM Specifications

| Attribute | Value |
|-----------|-------|
| **VM Name** | db-jim |
| **Instance Type** | t3.small |
| **RAM** | 2 GB |
| **Storage** | 32 GB |
| **Network** | Private Subnet (LAN) |
| **IP Address** | 10.0.2.10 |
| **External Access** | No |
| **OS** | Ubuntu 22.04 LTS |

## Purpose
- Store realistic telecommunications customer and billing data
- Serve as lateral movement target for compromised web server
- Provide data exfiltration opportunities for attackers
- Monitor database access patterns and privilege escalation attempts

## Key Services
- **MySQL Server**: Primary database service
- **Filebeat**: Log forwarding to ELK stack on soc-jim
- **Sample Data**: Realistic customer, billing, and employee records

## Database Contents
- **Customer Records**: Names, addresses, service plans, payment methods
- **Billing Data**: Invoices, payments, usage records
- **Employee Data**: Staff accounts with varying privilege levels
- **Call Records**: Telecommunications usage logs and metadata

## Security Posture
- **Intentional Vulnerabilities**: Weak passwords, excessive privileges, unencrypted data
- **Access Control**: Multiple user accounts with poor password policies
- **Monitoring**: Query logging and connection tracking
- **Backup Files**: Accessible database dumps for data exfiltration testing

## Network Access
- **Inbound**: MySQL (3306) from web-jim only, SSH (22) admin only
- **Outbound**: Log forwarding to soc-jim, system updates
