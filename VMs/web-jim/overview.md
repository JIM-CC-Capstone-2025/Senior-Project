# web-jim - Public Web Server

## Overview
Public-facing web server hosting the JIM telecommunications company website and customer portal. Serves as the primary entry point for attackers and the main honeypot component.

## VM Specifications

| Attribute | Value |
|-----------|-------|
| **VM Name** | web-jim |
| **Instance Type** | t3.small |
| **RAM** | 2 GB |
| **Storage** | 32 GB |
| **Network** | Public Subnet (DMZ) |
| **IP Address** | 10.0.1.100 |
| **External Access** | Yes (Elastic IP) |
| **OS** | Ubuntu 22.04 LTS |

## Purpose
- Host realistic telecommunications company website
- Provide customer portal with billing and account management
- Serve as primary attack vector for web-based exploits
- Connect to backend database for dynamic content

## Key Services
- **NGINX**: Web server handling HTTP/HTTPS traffic
- **PHP**: Server-side scripting for customer portal
- **Filebeat**: Log forwarding to ELK stack on soc-jim
- **JIM Telecom Website**: Custom telecommunications company site

## Security Posture
- **Intentional Vulnerabilities**: SQL injection, XSS, file upload weaknesses
- **Monitoring**: Comprehensive access logging and real-time monitoring
- **SSL/TLS**: Configured for encrypted connections
- **Database Connectivity**: Direct connection to db-jim for customer data

## Network Access
- **Inbound**: HTTP (80), HTTPS (443), SSH (22) admin only
- **Outbound**: MySQL to db-jim, log forwarding to soc-jim
