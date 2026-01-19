# SFTP Server Connections Update

## Overview

This document provides updated instructions for connecting to the Secure File Transfer Protocol (SFTP) server. **The 
only change is the port used for the connection**; all other settings and methods for connecting remain the same. Please 
ensure that your SFTP client is configured with the new port number as detailed below.

## Server Information

- **SFTP Server Address:** `1.studio.boardgamearena.com`
- **New Port:** `2022` (previously `22`)
- **Username:** `your-username`
- **Password:** `your-password`
- **Private Key:** `path/to/your/private/key` (if using key-based authentication)

## Prerequisites

Before connecting to the SFTP server, ensure you have the following:

1. **SFTP Client:** A client application capable of handling SFTP connections (e.g., FileZilla, WinSCP, Cyberduck, or 
command-line tools like `sftp` or `scp`).
2. **Authentication Details:** Your username and password or private key for authentication.
3. **Network Access:** Ensure that your firewall or network policies allow outbound traffic on the new port (`2022`).

## Connection Instructions

### Update Your SFTP Client Configuration

If you are using an SFTP client, you only need to update the port number in your existing configuration:

1. **Open FileZilla** or your preferred SFTP client.
2. **Edit Your Existing Site**:
    - Go to `File > Site Manager` and select your previously configured site (e.g., `SFTP Server`).
    - **Change the Port** to `2022`.
    - Ensure that all other settings (Host, Protocol, Login Type, User, Password/Key File) remain unchanged.
3. **Click Connect** to establish the connection using the new port.

### Using the Command Line

For users who connect via the command line, you only need to specify the new port:

1. **Password Authentication**:
    ```bash
    sftp -P 2022 your-username@1.studio.boardgamearena.com
    ```
   You will be prompted to enter your password.

2. **Key-Based Authentication**:
    ```bash
    sftp -P 2022 -i /path/to/your/private/key your-username@1.studio.boardgamearena.com
    ```

   Replace `/path/to/your/private/key` with the actual path to your private key file.

3. **Navigate and Transfer Files**:
    - Use standard SFTP commands to navigate directories and transfer files. For example:
        - `ls` - List files and directories.
        - `cd [directory]` - Change directory.
        - `get [filename]` - Download a file.
        - `put [filename]` - Upload a file.
        - `exit` - Close the connection.

## Security Considerations

- **Password Management:** Do not share your credentials.
- **Key-Based Authentication:** Prefer key-based authentication over password-based authentication where possible.
- **Session Management:** Always log out from the SFTP server after your session is complete.

## Troubleshooting

If you encounter issues connecting to the SFTP server, consider the following:

- **Network Issues:** Ensure that your network settings allow SFTP connections on the new port (`2022`).
- **Authentication Errors:** Double-check your username, password, and key file path. Ensure that your private key has the correct permissions (`chmod 600`).
- **Client Configuration:** Verify that your SFTP client is correctly configured with the updated port.

For further assistance, contact your system administrator or refer to the client’s documentation.

## Contact Information

If you require further assistance or encounter any issues, please reach out to the IT support team:

- **Forum:** https://studio.boardgamearena.com/forum/viewtopic.php?f=12&t=38343
- **Discord:** https://discord.com/invite/4q7dz4F5d3
- **Support:** https://studio.boardgamearena.com/support
