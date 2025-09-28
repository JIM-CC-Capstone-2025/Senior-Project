# soc-jim - Security Operations Center

## Overview
Centralized monitoring and logging platform using the ELK stack to collect, analyze, and visualize security events from all honeypot components. Serves as the primary research and analysis hub for attack data.

## VM Specifications

| Attribute | Value |
|-----------|-------|
| **VM Name** | soc-jim |
| **Instance Type** | t3.large |
| **RAM** | 8 GB |
| **Storage** | 80 GB |
| **Network** | Private Subnet (LAN) |
| **IP Address** | 10.0.2.20 |
| **External Access** | No |
| **OS** | Ubuntu 22.04 LTS |

## Purpose
- Collect and aggregate logs from all honeypot VMs
- Provide real-time monitoring and alerting capabilities
- Enable analysis of attack patterns and threat intelligence
- Generate security dashboards and reports for research

## Key Services
- **Elasticsearch**: Search and analytics engine for log storage
- **Kibana**: Web-based visualization and dashboard interface
- **Logstash**: Data processing pipeline for log ingestion
- **ELK Stack Management**: Centralized logging infrastructure

## Data Sources
- **Web Server Logs**: HTTP access, PHP application, authentication attempts
- **Database Logs**: MySQL queries, connections, authentication failures
- **Jump Server Logs**: SSH sessions, command history, system activities
- **System Metrics**: CPU, memory, disk, and network usage from all VMs

## Monitoring Capabilities
- **Real-time Dashboards**: Attack overview, web security, database activity
- **Alerting System**: Critical security events and threshold breaches
- **Data Export**: Weekly exports for offline analysis and research
- **Threat Intelligence**: Attack pattern analysis and reporting

## Network Access
- **Inbound**: Beats input (5044) from all VMs, Kibana web (5601) admin access
- **Outbound**: Internet for threat intelligence feeds and updates
