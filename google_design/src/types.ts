export interface Project {
  id: string;
  title: string;
  category: string;
  description: string;
  techStack: string[];
  metrics: {
    latency?: string;
    uptime?: string;
    throughput?: string;
    scale?: string;
  };
  details: string[];
  visualType: 'kube' | 'cdn' | 'db' | 'pipeline';
}

export interface Service {
  id: string;
  name: string;
  icon: string;
  tagline: string;
  description: string;
  basePrice: number;
  deliveryTime: string;
  features: string[];
}

export interface ContactPayload {
  name: string;
  email: string;
  projectType: string;
  message: string;
}
