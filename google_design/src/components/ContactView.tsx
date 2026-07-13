import React, { useState } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import { Send, Terminal, Mail, CheckCircle, Shield, Code, Server, Info } from 'lucide-react';
import { ContactPayload } from '../types';

export default function ContactView() {
  const [formData, setFormData] = useState<ContactPayload>({
    name: '',
    email: '',
    projectType: 'architecture',
    message: ''
  });

  const [formStatus, setFormStatus] = useState<'idle' | 'transmitting' | 'success'>('idle');
  const [transmissionLogs, setTransmissionLogs] = useState<string[]>([]);
  const [errors, setErrors] = useState<{ name?: string; email?: string; message?: string }>({});

  const validateForm = () => {
    const newErrors: typeof errors = {};
    if (!formData.name.trim()) newErrors.name = 'FULL_NAME is required';
    if (!formData.email.trim()) {
      newErrors.email = 'EMAIL_ADDRESS is required';
    } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
      newErrors.email = 'INVALID_EMAIL_PAYLOAD';
    }
    if (!formData.message.trim()) newErrors.message = 'MESSAGE_PAYLOAD cannot be empty';
    
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
    if (errors[name as keyof typeof errors]) {
      setErrors(prev => ({ ...prev, [name]: undefined }));
    }
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!validateForm()) return;

    setFormStatus('transmitting');
    setTransmissionLogs([
      'Establishing SSL connection to VERTEX gateway...',
      'Encrypting contact payload using RSA-4096...',
      'Verifying DNS entries and route path parameters...',
      'Routing data packets via active global CDN nodes...'
    ]);

    // Simulate progressive transmission logs
    setTimeout(() => {
      setTransmissionLogs(prev => [...prev, 'Handshake verified. Node response: 200 OK.']);
    }, 600);

    setTimeout(() => {
      setTransmissionLogs(prev => [...prev, 'Data synced successfully. Storing payload in secure cache.']);
    }, 1100);

    setTimeout(() => {
      setFormStatus('success');
    }, 1800);
  };

  const resetForm = () => {
    setFormData({ name: '', email: '', projectType: 'architecture', message: '' });
    setFormStatus('idle');
    setTransmissionLogs([]);
  };

  return (
    <motion.div 
      initial={{ opacity: 0, y: 15 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, y: -15 }}
      transition={{ duration: 0.4 }}
      className="grid grid-cols-1 md:grid-cols-12 gap-12 items-start"
    >
      {/* Left Side: Messaging */}
      <div className="md:col-span-5 space-y-8">
        <header className="space-y-4">
          <motion.div 
            initial={{ opacity: 0, scale: 0.95 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ delay: 0.1 }}
            className="inline-flex items-center gap-2 px-3 py-1 bg-white/10 border border-white/20 text-white font-semibold backdrop-blur-md rounded-full"
          >
            <span className="w-2.5 h-2.5 bg-indigo-400 rounded-full animate-pulse"></span>
            <span className="font-mono text-[10px] text-indigo-200 uppercase tracking-widest font-bold">Available for hire</span>
          </motion.div>
          
          <h1 className="font-sans text-4xl md:text-5xl font-bold tracking-tighter text-white leading-tight">
            Architecting Your <span className="text-indigo-400">Next Big Idea.</span>
          </h1>
          
          <p className="font-sans text-base md:text-lg text-white/70 max-w-md leading-relaxed">
            Whether you're looking for a technical audit, a scalable cloud architecture, or a full-stack engineering partner, let's start the conversation. 
          </p>
        </header>

        {/* Contact Metadata Mockup */}
        <div className="hidden md:block p-5 glass-card border border-white/10 rounded-2xl relative overflow-hidden group hover:border-indigo-400/40 transition-colors duration-300">
          <div className="absolute top-4 right-4 flex gap-1.5">
            <div className="w-2.5 h-2.5 rounded-full bg-red-500/60"></div>
            <div className="w-2.5 h-2.5 rounded-full bg-yellow-500/30"></div>
            <div className="w-2.5 h-2.5 rounded-full bg-green-500/30"></div>
          </div>
          <div className="font-mono text-xs space-y-4 p-2">
            <p className="text-white/40 italic">// Contact Metadata</p>
            <div className="space-y-3">
              <div className="flex gap-4">
                <span className="text-indigo-400 select-none">email:</span>
                <a className="text-indigo-300 hover:text-white hover:underline transition-all cursor-pointer" href="mailto:architect@dev.null">architect@dev.null</a>
              </div>
              <div className="flex gap-4">
                <span className="text-indigo-400 select-none">location:</span>
                <span className="text-white/80">Remote / Global</span>
              </div>
              <div className="flex gap-4">
                <span className="text-indigo-400 select-none">socials:</span>
                <div className="flex gap-3">
                  <a className="text-white/60 hover:text-white transition-colors cursor-pointer font-sans" href="#github">GitHub</a>
                  <span className="text-white/20">/</span>
                  <a className="text-white/60 hover:text-white transition-colors cursor-pointer font-sans" href="#linkedin">LinkedIn</a>
                  <span className="text-white/20">/</span>
                  <a className="text-white/60 hover:text-white transition-colors cursor-pointer font-sans" href="#twitter">Twitter</a>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Desktop Decorative Map Area */}
        <div className="relative w-full h-48 rounded-2xl overflow-hidden border border-white/10 md:block hidden">
          <div 
            className="w-full h-full bg-cover bg-center grayscale contrast-125 opacity-40 hover:scale-105 transition-transform duration-500" 
            style={{ backgroundImage: `url('https://lh3.googleusercontent.com/aida-public/AB6AXuDjyPpuyf9DPkTRZyUa-bdin_E3Q_zlzT_E_e6z3jH69M2nmWkVsrdFVxf8VSdZDkqT8DPRJ9hjOPM10Y2_IxJx4IEzovfSVUZMDcGmFSlCwZiEV_XKogtW3qY6tQAZAu5z199LSBtgDXRXf4HNKnCQqvUdDD8JWRYNKPMFsbaqmjb95uuyoCqMhNWt291OmCCqCqY03WBRNcZ25-ROg_h7PdmHqrbmYS8-pdyjQSzc66kCY-QapI4LF6QwrixiwJ4iiTUJk3XyrGNm')` }}
          ></div>
          <div className="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
          <div className="absolute bottom-4 left-4 font-mono text-xs text-indigo-400 flex items-center gap-2">
            <span className="w-2 h-2 bg-indigo-400 rounded-full animate-ping"></span>
            <span>&gt; LOCATE_GLOBAL_NODE: ACTIVE</span>
          </div>
        </div>
      </div>

      {/* Right Side: Contact Form Window */}
      <div className="md:col-span-7">
        <div className="glass-card-heavy border border-white/15 p-6 md:p-10 rounded-2xl relative hover:border-indigo-400/25 transition-all duration-300 shadow-2xl">
          {/* Window Controls Mockup */}
          <div className="absolute top-4 left-4 flex gap-2">
            <div className="w-3 h-3 rounded-full bg-red-500/60"></div>
            <div className="w-3 h-3 rounded-full bg-yellow-500/30"></div>
            <div className="w-3 h-3 rounded-full bg-green-500/30"></div>
          </div>
          <div className="absolute top-4 right-8 font-mono text-xs text-white/40 select-none">
            contact_form.v2.tsx
          </div>

          <AnimatePresence mode="wait">
            {formStatus === 'idle' && (
              <motion.form 
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0 }}
                onSubmit={handleSubmit}
                className="mt-8 space-y-6"
                id="contact-form"
              >
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                  {/* Name Input */}
                  <div className="space-y-2">
                    <label 
                      className={`font-sans font-medium text-xs transition-colors duration-200 ${
                        errors.name ? 'text-red-400' : 'text-white/60'
                      }`}
                      htmlFor="name"
                    >
                      FULL_NAME
                    </label>
                    <input 
                      className={`w-full bg-slate-950/20 border text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans placeholder:text-white/30 ${
                        errors.name ? 'border-red-500/60' : 'border-white/10'
                      }`}
                      id="name" 
                      name="name" 
                      value={formData.name}
                      onChange={handleInputChange}
                      placeholder="e.g. Alan Turing" 
                      type="text"
                    />
                    {errors.name && (
                      <p className="text-red-400 font-mono text-[11px]">&gt; {errors.name}</p>
                    )}
                  </div>

                  {/* Email Input */}
                  <div className="space-y-2">
                    <label 
                      className={`font-sans font-medium text-xs transition-colors duration-200 ${
                        errors.email ? 'text-red-400' : 'text-white/60'
                      }`}
                      htmlFor="email"
                    >
                      EMAIL_ADDRESS
                    </label>
                    <input 
                      className={`w-full bg-slate-950/20 border text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans placeholder:text-white/30 ${
                        errors.email ? 'border-red-500/60' : 'border-white/10'
                      }`}
                      id="email" 
                      name="email" 
                      value={formData.email}
                      onChange={handleInputChange}
                      placeholder="e.g. alan@bletchley.com" 
                      type="email"
                    />
                    {errors.email && (
                      <p className="text-red-400 font-mono text-[11px]">&gt; {errors.email}</p>
                    )}
                  </div>
                </div>

                {/* Project Type Select */}
                <div className="space-y-2">
                  <label className="font-sans font-medium text-xs text-white/60" htmlFor="project-type">PROJECT_TYPE</label>
                  <select 
                    className="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans cursor-pointer"
                    id="project-type" 
                    name="projectType"
                    value={formData.projectType}
                    onChange={handleInputChange}
                  >
                    <option value="architecture" className="bg-[#0f172a] text-white">System Architecture</option>
                    <option value="fullstack" className="bg-[#0f172a] text-white">Full-Stack Development</option>
                    <option value="consultation" className="bg-[#0f172a] text-white">Technical Consultation</option>
                    <option value="audit" className="bg-[#0f172a] text-white">Code & Security Audit</option>
                    <option value="other" className="bg-[#0f172a] text-white">Other / Custom</option>
                  </select>
                </div>

                {/* Message Payload Textarea */}
                <div className="space-y-2">
                  <label 
                    className={`font-sans font-medium text-xs transition-colors duration-200 ${
                      errors.message ? 'text-red-400' : 'text-white/60'
                    }`}
                    htmlFor="message"
                  >
                    MESSAGE_PAYLOAD
                  </label>
                  <textarea 
                    className={`w-full bg-slate-950/20 border text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans placeholder:text-white/30 resize-none ${
                      errors.message ? 'border-red-500/60' : 'border-white/10'
                    }`}
                    id="message" 
                    name="message" 
                    value={formData.message}
                    onChange={handleInputChange}
                    placeholder="Describe your project scope and objectives..." 
                    rows={5}
                  />
                  {errors.message && (
                    <p className="text-red-400 font-mono text-[11px]">&gt; {errors.message}</p>
                  )}
                </div>

                {/* Submit button */}
                <button 
                  className="w-full bg-indigo-500/85 hover:bg-indigo-500 border border-white/10 text-white font-sans font-semibold py-4 rounded-lg active:scale-[0.98] transition-all flex items-center justify-center gap-3 group cursor-pointer shadow-lg shadow-indigo-500/25"
                  type="submit"
                >
                  <span>INITIALIZE_CONTACT</span>
                  <Send className="w-4 h-4 group-hover:translate-x-1 transition-transform text-indigo-200" />
                </button>
              </motion.form>
            )}

            {/* Transmission Logging Phase */}
            {formStatus === 'transmitting' && (
              <motion.div 
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0 }}
                className="mt-8 space-y-4 font-mono text-xs py-8"
              >
                <div className="flex items-center gap-3 text-indigo-400">
                  <div className="w-4 h-4 border-2 border-indigo-400 border-t-transparent rounded-full animate-spin"></div>
                  <span className="font-bold tracking-widest text-sm uppercase">Transmitting Payload...</span>
                </div>
                
                <div className="p-4 bg-slate-950/40 border border-white/10 rounded-xl space-y-2 max-h-60 overflow-y-auto">
                  {transmissionLogs.map((log, idx) => (
                    <motion.p 
                      initial={{ opacity: 0, x: -10 }}
                      animate={{ opacity: 1, x: 0 }}
                      key={idx} 
                      className="text-emerald-400"
                    >
                      &gt; {log}
                    </motion.p>
                  ))}
                </div>
              </motion.div>
            )}

            {/* Success Phase */}
            {formStatus === 'success' && (
              <motion.div 
                initial={{ opacity: 0, scale: 0.95 }}
                animate={{ opacity: 1, scale: 1 }}
                exit={{ opacity: 0 }}
                className="mt-8 space-y-6 text-center py-6"
              >
                <div className="inline-flex items-center justify-center p-4 bg-emerald-500/10 rounded-full border border-emerald-500/30">
                  <CheckCircle className="w-12 h-12 text-emerald-400" />
                </div>
                
                <div className="space-y-2">
                  <h3 className="font-mono text-lg font-bold text-white">CONTACT_INITIALIZATION: SUCCESS</h3>
                  <p className="font-sans text-sm text-white/70 max-w-sm mx-auto">
                    Payload received and validated. Our architect will review the system metrics and respond within 24 hours.
                  </p>
                </div>

                {/* Echoed Payload */}
                <div className="text-left bg-slate-950/40 border border-white/10 p-4 rounded-xl font-mono text-[11px] text-white/70 space-y-1">
                  <span className="text-white/40 italic">// Transmitted Payload Log</span>
                  <p><span className="text-indigo-400">name:</span> "{formData.name}"</p>
                  <p><span className="text-indigo-400">email:</span> "{formData.email}"</p>
                  <p><span className="text-indigo-400">type:</span> "{formData.projectType}"</p>
                  <p><span className="text-indigo-400">status:</span> "QUEUED_FOR_REVIEW"</p>
                </div>

                <button 
                  onClick={resetForm}
                  className="px-6 py-3 bg-white/5 border border-white/10 rounded-lg font-sans text-xs text-white hover:bg-white/10 hover:border-white/20 transition-all cursor-pointer"
                >
                  &gt; Send Another Message
                </button>
              </motion.div>
            )}
          </AnimatePresence>
        </div>
      </div>

      {/* Mobile-Only Contact Details */}
      <div className="md:hidden mt-8 pt-8 border-t border-white/10 space-y-6">
        <div className="space-y-1">
          <p className="font-mono text-xs text-indigo-400 uppercase tracking-wider">Direct Channel</p>
          <a className="font-mono text-xl font-semibold text-indigo-300 hover:text-white hover:underline" href="mailto:architect@dev.null">
            architect@dev.null
          </a>
        </div>
        <div className="flex gap-6">
          <a className="font-sans text-sm text-white/60 hover:text-white" href="#github">GitHub</a>
          <a className="font-sans text-sm text-white/60 hover:text-white" href="#linkedin">LinkedIn</a>
          <a className="font-sans text-sm text-white/60 hover:text-white" href="#twitter">Twitter</a>
        </div>
      </div>
    </motion.div>
  );
}
