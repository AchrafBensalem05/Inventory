import { contextBridge } from 'electron';

contextBridge.exposeInMainWorld('oilgas', {
    platform: process.platform,
});
