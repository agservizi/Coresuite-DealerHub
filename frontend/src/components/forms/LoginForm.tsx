"use client";

import { useState } from "react";
import type { FormEvent } from "react";
import { useRouter } from "next/navigation";
import { useAuth } from "@/context/AuthContext";

export function LoginForm() {
  const { login } = useAuth();
  const router = useRouter();
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    const formData = new FormData(event.currentTarget);
    setError(null);
    setLoading(true);
    const email = String(formData.get("email"));
    const password = String(formData.get("password"));
    try {
      await login(email, password);
      router.replace("/dashboard");
    } catch (err) {
      setError((err as Error).message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <form className="space-y-3" onSubmit={handleSubmit}>
      <label className="text-sm">
        Email
        <input
          name="email"
          type="email"
          required
          className="mt-1 w-full rounded-xl border border-gray-300 bg-gray-100 p-3 text-gray-900"
        />
      </label>
      <label className="text-sm">
        Password
        <input
          name="password"
          type="password"
          required
          className="mt-1 w-full rounded-xl border border-gray-300 bg-gray-100 p-3 text-gray-900"
        />
      </label>
      {error && <p className="text-sm text-rose-400">{error}</p>}
      <button
        type="submit"
        className="w-full rounded-xl bg-emerald-500 py-3 font-semibold text-white mb-4"
        disabled={loading}
      >
        {loading ? "Accesso in corso..." : "Accedi"}
      </button>
      <p className="text-center text-xs text-gray-500">
        <a href="/forgot-password" className="text-emerald-600">
          Recupera password
        </a>
      </p>
    </form>
  );
}
