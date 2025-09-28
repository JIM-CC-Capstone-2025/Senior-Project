░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░<br>
░░░░░░░██████╗░██████╗░██████╗░░░░░░██╗░░██╗░██╗██████╗░░░░░░░<br>
░░░░░░██╔════╝██╔════╝██╔════╝░░░░░░██║░░██║███║╚════██╗░░░░░░<br>
░░░░░░██║░░░░░██║░░░░░██║░░░░░█████╗███████║╚██║░█████╔╝░░░░░░<br>
░░░░░░██║░░░░░██║░░░░░██║░░░░░╚════╝╚════██║░██║██╔═══╝░░░░░░░<br>
░░░░░░╚██████╗╚██████╗╚██████╗░░░░░░░░░░░██║░██║███████╗░░░░░░<br>
░░░░░░░╚═════╝░╚═════╝░╚═════╝░░░░░░░░░░░╚═╝░╚═╝╚══════╝░░░░░░<br>
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░<br>



## Project Overview
JIM Telecommunications is a realistic telecommunications honeypot environment on AWS with the purpose to attract, capture, and analyze real-world cyber attacks targeting critical infrastructure. By simulating JIM Telecommunications with customer portals, billing systems, and network management platforms, we create a controlled environment to study attacker methodologies ranging from initial web exploitation to advanced persistent threats involving lateral movement and data exfiltration. Through comprehensive ELK stack monitoring, the project captures intelligence on threat actor behavior and techniques specifically targeting telecommunications infrastructure, providing valuable insights for defending critical national infrastructure and contributing to the broader cybersecurity knowledge base.

## VM Infrastructure

| VM Name | RAM | Storage | Network | IP Address | External Access | Primary Services | Role in Honeypot |
|---------|-----|---------|---------|------------|-----------------|------------------|------------------|
| **web-jim** | 8 GB | 32 GB | Public Subnet | 10.0.1.100 | Yes (Elastic IP) | NGINX, PHP, Customer Portal | Primary attack vector and public entry point |
| **db-jim** | 4 GB | 32 GB | Private Subnet | 10.0.2.10 | No | MySQL, Customer Data | Lateral movement target and data repository |
| **soc-jim** | 16 GB | 100 GB | SOC Subnet | 10.0.3.10 | No | ELK Stack, Monitoring | Centralized logging and threat analysis |
| **jump-jim** | 8 GB | 32 GB | Private Subnet | 10.0.2.30 | No | SSH, Telnet, Management Tools | Internal honeypot for privilege escalation |
