# JIM Telecommunications - Complete Task Checklist

## AWS Access Resolution

### AWS Infrastructure Access
- [X] Validate AWS connectivity issues
- [X] Verify IAM user permissions and policies
- [X] Check security groups for SSH/RDP access rules
- [X] Verify VPC and subnet configurations
- [X] Test connection from different networks (campus vs external)
- [X] Contact AWS support if needed

### Validate Shared Account Issues
- [X] Contact Adam
- [ ] Confirm account limits

## PROJECT MANAGEMENT & DOCUMENTATION

### Project Setup
- [X] Finalize budget selection (Low: $29.64, Mid: $168.90, Mid-High: $224.38, High: $349.48)
- [X] Update project timeline based on current date (Sept 28, 2025)
- [ ] Establish backup/recovery procedures for project data
- [ ] Set up regular progress tracking (weekly exports mentioned in timeline)

### Documentation Management
- [ ] Centralize configuration documentation in GitHub repository
- [ ] Create AWS deployment scripts based on vCenter configurations
- [ ] Document network architecture with updated IP schemes for AWS
- [ ] Create incident response procedures for honeypot management
- [ ] Establish data retention policies for collected logs

## AWS INFRASTRUCTURE DEPLOYMENT

### VPC and Networking
- [ ] Create VPC with appropriate CIDR blocks
- [ ] Public subnet for web-jim (DMZ equivalent)
- [ ] Private subnet for internal services (LAN equivalent)
- [ ] Configure Internet Gateway
- [ ] Set up NAT Gateway for private subnet internet access

### Configure Security Groups
- [ ] Web server: HTTP/HTTPS (80/443), SSH (22)
- [ ] Database: MySQL (3306) from web server only
- [ ] SOC: Internal monitoring ports, SSH access
- [ ] Jump box: SSH/Telnet (22/23), limited internal access
- [ ] Management: Restricted SSH access for administration

### EC2 Instance Provisioning

#### Deploy web-jim (DMZ)
- [ ] Launch EC2 instance with appropriate sizing
- [ ] Assign Elastic IP for consistent external access
- [ ] Configure security groups for public web access

#### Deploy db-jim (Private)
- [ ] Launch database server instance
- [ ] Configure EBS volumes for database storage
- [ ] Set up automated backups

#### Deploy soc-jim (Private)
- [ ] Launch monitoring server with sufficient resources
- [ ] Configure storage for log retention
- [ ] Set up monitoring tools

#### Deploy jump-jim (Private)
- [ ] Configure as honeypot entry point
- [ ] Set up logging for all access attempts

## INDIVIDUAL VM CONFIGURATIONS

## web-jim (Public Web Server)

### Operating System Setup
- [ ] Create relevant accounts
- [ ] Set up SSH keys
- [ ] Any other configuration

### NGINX Configuration
- [ ] Install and configure NGINX
- [ ] Deploy JIM telecommunications website
- [ ] Configure SSL/TLS with Let's Encrypt or self-signed certificates
- [ ] Set up access and error logging
- [ ] Configure rate limiting and basic DDoS protection

### Web Application Setup
- [ ] Deploy customer portal with PHP
- [ ] Configure database connections to db-jim
- [ ] Implement basic authentication mechanisms
- [ ] Add intentional vulnerabilities for honeypot purposes
- [ ] Potentially set up file upload capabilities (potential attack vector)

### Monitoring and Logging
- [ ] Install log forwarding agent
- [ ] Configure detailed access logging
- [ ] Set up real-time log streaming to SOC
- [ ] Monitor for common web attacks (SQL injection, XSS, etc.)

## db-jim (Database Server)

### Database Installation
- [ ] Install MySQL/MariaDB
- [ ] Configure root password and create service accounts
- [ ] Set up database for telecommunications data
- [ ] Configure backup procedures

### Data Population
- [ ] Create customer database schema
- [ ] Generate realistic customer data
- [ ] Create billing and service records
- [ ] Add employee and infrastructure data
- [ ] Implement user account management tables

### Security Configuration
- [ ] Configure firewall rules (database ports only from web server)
- [ ] Set up database user permissions
- [ ] Enable query logging for monitoring
- [ ] Configure SSL for database connections

### Honeypot Elements
- [ ] Add intentional misconfigurations/weaknesses
- [ ] Create privileged accounts with weak passwords
- [ ] Include sensitive-looking data for exfiltration attempts

### Monitoring Setup
- [ ] Install database monitoring tools
- [ ] Configure query logging
- [ ] Set up alerts for suspicious database activity
- [ ] Install log forwarding to SOC

## soc-jim (Security Operations Center)

### Monitoring Platform Setup
- [ ] Set up Elasticsearch for log storage
- [ ] Configure Kibana for log visualization
- [ ] Install log collection solution

### Agent Deployment
- [ ] Install agents on all other VMs
- [ ] Configure log forwarding from all systems
- [ ] Test agent connectivity and data flow
- [ ] Set up centralized agent management

### Alerting Configuration
- [ ] Configure alerts for failed login attempts
- [ ] Set up notifications for suspicious file access
- [ ] Create alerts for unusual network traffic
- [ ] Configure threshold-based alerting (CPU, memory, disk usage)
- [ ] Set up external notifications (email, Slack?)

### Dashboard Creation
- [ ] Create real-time monitoring dashboards
- [ ] Set up threat intelligence feeds
- [ ] Configure automated threat detection rules
- [ ] Create incident response workflows

### Data Management
- [ ] Configure log retention policies
- [ ] Set up automated log archival
- [ ] Create backup procedures for collected data
- [ ] Implement data export capabilities for analysis

## jump-jim (Honeypot Jump Server)

### Honeypot Services Setup
- [ ] Configure SSH honeypot with weak credentials
- [ ] Set up Telnet service for legacy protocol attacks
- [ ] Install and configure additional honeypot services
- [ ] Create fake user accounts and home directories

### Vulnerability Implementation
- [ ] Configure intentional SSH misconfigurations
- [ ] Set up weak authentication mechanisms
- [ ] Create false privilege escalation opportunities
- [ ] Add fake sensitive files and directories

### Logging Configuration
- [ ] Enable comprehensive SSH logging
- [ ] Configure command logging for all sessions
- [ ] Set up session recording capabilities
- [ ] Monitor for lateral movement attempts

### Network Simulation
- [ ] Configure network discovery tools responses
- [ ] Set up fake internal network mappings
- [ ] Create simulated internal services
- [ ] Configure port scanning responses

## TELECOMMUNICATIONS SERVICES IMPLEMENTATION

### Customer Portal Enhancement

#### Potential Portal Functionality
- [ ] Implement customer login system
- [ ] Create account management interfaces
- [ ] Set up billing and payment
- [ ] Set up support ticket system

#### Database Integration
- [ ] Connect portal to customer database
- [ ] Implement session management
- [ ] Add audit logging for user actions
- [ ] Configure data validation and sanitization

#### Intentional Vulnerabilities
- [ ] Add SQL injection opportunities
- [ ] Create XSS vulnerabilities
- [ ] Implement weak session management
- [ ] Add file upload vulnerabilities

### Billing and Account Management

#### Billing System Setup
- [ ] Create billing database schema
- [ ] Generate realistic billing data
- [ ] Implement payment processing interfaces
- [ ] Set up automated billing cycles

#### Integration Testing
- [ ] Connect billing to customer portal
- [ ] Test payment processing workflows
- [ ] Verify data consistency across systems
- [ ] Implement audit trails
