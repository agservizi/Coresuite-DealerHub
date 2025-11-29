"use client";

import { Navbar } from "@/components/layout/Navbar";
import { Sidebar } from "@/components/layout/Sidebar";
import { useAuthGuard } from "@/hooks/useAuthGuard";
import { useState } from "react";
import type { ReactNode } from "react";
import clsx from "clsx";

export function AppShell({ children }: { children: ReactNode }) {
  const { loading } = useAuthGuard();
  const [mobileSidebarOpen, setMobileSidebarOpen] = useState(false);
  const [sidebarCollapsed, setSidebarCollapsed] = useState(false);

  if (loading) {
    return (
      <div className="flex min-h-screen items-center justify-center text-gray-900">
        Caricamento sessione...
      </div>
    );
  }

  return (
    <div className="flex min-h-screen bg-gray-100 text-gray-900">
      <Sidebar isOpen={mobileSidebarOpen} onClose={() => setMobileSidebarOpen(false)} collapsed={sidebarCollapsed} onToggleCollapsed={setSidebarCollapsed} />
      <div className="flex flex-1 flex-col">
        <Navbar onMenuClick={() => setMobileSidebarOpen(true)} collapsed={sidebarCollapsed} />
        <main className={clsx("flex-1 bg-gray-100 px-4 py-8", sidebarCollapsed ? "md:pl-16" : "md:pl-64")}>
          {children}
        </main>
      </div>
    </div>
  );
}
