<script setup lang="ts">
import { useLanguage } from '@/composables/useLanguage';
import { Link } from '@inertiajs/vue3';
import { Facebook, Linkedin, Twitter, Youtube } from 'lucide-vue-next';

const { t } = useLanguage();
</script>

<template>
    <footer class="border-t bg-muted/50">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                <!-- About -->
                <div>
                    <h3 class="mb-4 text-lg font-semibold">
                        {{ t.about }} {{ t.jobPortal }}
                    </h3>
                    <ul class="space-y-2">
                        <li>
                            <Link
                                href="/about"
                                class="text-sm text-muted-foreground hover:text-primary"
                            >
                                {{ t.about }}
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/contact"
                                class="text-sm text-muted-foreground hover:text-primary"
                            >
                                {{ t.contact }}
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/terms"
                                class="text-sm text-muted-foreground hover:text-primary"
                            >
                                {{ t.terms }}
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/privacy"
                                class="text-sm text-muted-foreground hover:text-primary"
                            >
                                {{ t.privacy }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- For Candidates -->
                <div>
                    <h3 class="mb-4 text-lg font-semibold">
                        {{ t.forCandidates }}
                    </h3>
                    <ul class="space-y-2">
                        <li>
                            <Link
                                href="/jobs"
                                class="text-sm text-muted-foreground hover:text-primary"
                            >
                                {{ t.findJobs }}
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/companies"
                                class="text-sm text-muted-foreground hover:text-primary"
                            >
                                {{ t.companyList }}
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/cv-templates"
                                class="text-sm text-muted-foreground hover:text-primary"
                            >
                                {{ t.cvTemplates }}
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/career-guide"
                                class="text-sm text-muted-foreground hover:text-primary"
                            >
                                {{ t.careerGuide }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- For Employers -->
                <div>
                    <h3 class="mb-4 text-lg font-semibold">
                        {{ t.forEmployers }}
                    </h3>
                    <ul class="space-y-2">
                        <li>
                            <Link
                                href="/employer/register"
                                class="text-sm text-muted-foreground hover:text-primary"
                            >
                                {{ t.postJob }}
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/employer/pricing"
                                class="text-sm text-muted-foreground hover:text-primary"
                            >
                                {{ t.pricing }}
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/employer/support"
                                class="text-sm text-muted-foreground hover:text-primary"
                            >
                                {{ t.support }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Social -->
                <div>
                    <h3 class="mb-4 text-lg font-semibold">
                        {{ t.followUs }}
                    </h3>
                    <div class="flex space-x-4">
                        <a
                            href="#"
                            class="text-muted-foreground hover:text-primary"
                        >
                            <Facebook class="h-5 w-5" />
                        </a>
                        <a
                            href="#"
                            class="text-muted-foreground hover:text-primary"
                        >
                            <Linkedin class="h-5 w-5" />
                        </a>
                        <a
                            href="#"
                            class="text-muted-foreground hover:text-primary"
                        >
                            <Twitter class="h-5 w-5" />
                        </a>
                        <a
                            href="#"
                            class="text-muted-foreground hover:text-primary"
                        >
                            <Youtube class="h-5 w-5" />
                        </a>
                    </div>
                    <p class="mt-4 text-sm text-muted-foreground">
                        © 2025 {{ t.jobPortal }}. {{ t.allRightsReserved }}.
                    </p>
                </div>
            </div>
        </div>
    </footer>
</template>
