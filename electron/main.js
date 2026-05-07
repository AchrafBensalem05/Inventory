import { app, BrowserWindow } from 'electron';
import { spawn } from 'node:child_process';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

let phpServer;

function getPhpExecutable() {
    if (app.isPackaged) {
        const phpName = process.platform === 'win32' ? 'php.exe' : 'php';
        return path.join(process.resourcesPath, 'php', phpName);
    }

    return 'php';
}

function getAppRoot() {
    return app.isPackaged ? path.join(process.resourcesPath, 'app') : process.cwd();
}

function startLaravel() {
    const phpPath = getPhpExecutable();
    const appRoot = getAppRoot();
    const artisanPath = path.join(appRoot, 'artisan');

    phpServer = spawn(phpPath, [artisanPath, 'serve', '--host=127.0.0.1', '--port=8000'], {
        cwd: appRoot,
        stdio: 'ignore',
    });
}

function createWindow() {
    const win = new BrowserWindow({
        width: 1400,
        height: 900,
        webPreferences: {
            preload: path.join(__dirname, 'preload.js'),
            contextIsolation: true,
            nodeIntegration: false,
        },
    });

    win.loadURL('http://127.0.0.1:8000');
}

app.whenReady().then(() => {
    startLaravel();
    setTimeout(createWindow, 2000);
});

app.on('before-quit', () => {
    if (phpServer) {
        phpServer.kill();
    }
});

app.on('window-all-closed', () => {
    if (process.platform !== 'darwin') {
        app.quit();
    }
});

app.on('activate', () => {
    if (BrowserWindow.getAllWindows().length === 0) {
        createWindow();
    }
});
