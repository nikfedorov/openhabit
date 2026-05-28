--
-- PostgreSQL database dump
--

\restrict dQwnvBDWhsBqHESZMqkC5dgOJs4EILvC55kW2LE2HkWDuQIYqoMkzN44aODmt1U

-- Dumped from database version 18.3
-- Dumped by pg_dump version 18.4 (Ubuntu 18.4-1.pgdg24.04+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: agent_conversation_messages; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.agent_conversation_messages (
    id character varying(36) NOT NULL,
    conversation_id character varying(36) NOT NULL,
    user_id bigint,
    agent character varying(255) NOT NULL,
    role character varying(25) NOT NULL,
    content text NOT NULL,
    attachments text NOT NULL,
    tool_calls text NOT NULL,
    tool_results text NOT NULL,
    usage text NOT NULL,
    meta text NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: agent_conversations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.agent_conversations (
    id character varying(36) NOT NULL,
    user_id bigint,
    title character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: ai_digests; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.ai_digests (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    date date NOT NULL,
    content text NOT NULL,
    habits_data json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: ai_digests_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.ai_digests_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ai_digests_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.ai_digests_id_seq OWNED BY public.ai_digests.id;


--
-- Name: ai_logs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.ai_logs (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    model character varying(255) NOT NULL,
    system_prompt text NOT NULL,
    user_prompt text NOT NULL,
    response text,
    input_tokens integer,
    output_tokens integer,
    duration_ms integer,
    is_successful boolean DEFAULT false NOT NULL,
    error text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: ai_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.ai_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ai_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.ai_logs_id_seq OWNED BY public.ai_logs.id;


--
-- Name: ai_models; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.ai_models (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    priority integer DEFAULT 0 NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    disabled_until timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    provider character varying(255) DEFAULT 'openrouter'::character varying NOT NULL
);


--
-- Name: ai_models_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.ai_models_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ai_models_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.ai_models_id_seq OWNED BY public.ai_models.id;


--
-- Name: ai_tones; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.ai_tones (
    id bigint NOT NULL,
    slug character varying(255) NOT NULL,
    name json NOT NULL,
    description json NOT NULL,
    icon character varying(255) NOT NULL,
    system_instruction text NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    is_default boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: ai_tones_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.ai_tones_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ai_tones_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.ai_tones_id_seq OWNED BY public.ai_tones.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: categories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.categories (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    name json NOT NULL,
    description json,
    slug character varying(255),
    is_active boolean DEFAULT true NOT NULL,
    sort_order integer DEFAULT 1 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: categories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.categories_id_seq OWNED BY public.categories.id;


--
-- Name: category_templates; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.category_templates (
    id bigint NOT NULL,
    name json NOT NULL,
    description json,
    slug character varying(255) NOT NULL,
    icon character varying(255),
    is_active boolean DEFAULT true NOT NULL,
    copy_by_default boolean DEFAULT true NOT NULL,
    sort_order integer DEFAULT 1 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: category_templates_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.category_templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: category_templates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.category_templates_id_seq OWNED BY public.category_templates.id;


--
-- Name: daily_notes; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.daily_notes (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    date date NOT NULL,
    content text NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: daily_notes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.daily_notes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: daily_notes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.daily_notes_id_seq OWNED BY public.daily_notes.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: habit_completions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.habit_completions (
    id bigint NOT NULL,
    habit_id bigint NOT NULL,
    user_id bigint NOT NULL,
    completed_at date NOT NULL,
    current_iteration smallint DEFAULT '1'::smallint NOT NULL,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: habit_completions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.habit_completions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: habit_completions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.habit_completions_id_seq OWNED BY public.habit_completions.id;


--
-- Name: habit_notifications; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.habit_notifications (
    id bigint NOT NULL,
    habit_id bigint NOT NULL,
    "time" character varying(5) NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    last_notified_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: habit_notifications_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.habit_notifications_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: habit_notifications_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.habit_notifications_id_seq OWNED BY public.habit_notifications.id;


--
-- Name: habit_templates; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.habit_templates (
    id bigint NOT NULL,
    category_template_id bigint,
    name json NOT NULL,
    description json,
    is_active boolean DEFAULT true NOT NULL,
    copy_by_default boolean DEFAULT true NOT NULL,
    show_in_templates boolean DEFAULT true NOT NULL,
    sort_order integer DEFAULT 1 NOT NULL,
    iterations_required smallint DEFAULT '1'::smallint NOT NULL,
    rrule character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: habit_templates_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.habit_templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: habit_templates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.habit_templates_id_seq OWNED BY public.habit_templates.id;


--
-- Name: habits; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.habits (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    category_id bigint,
    name json NOT NULL,
    description json,
    is_active boolean DEFAULT true NOT NULL,
    sort_order integer DEFAULT 1 NOT NULL,
    iterations_required smallint DEFAULT '1'::smallint NOT NULL,
    rrule character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


--
-- Name: habits_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.habits_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: habits_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.habits_id_seq OWNED BY public.habits.id;


--
-- Name: invoices; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.invoices (
    id bigint NOT NULL,
    title json NOT NULL,
    description json NOT NULL,
    slug character varying(255) NOT NULL,
    stars integer NOT NULL,
    subscription_period integer NOT NULL,
    invoice_link json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: invoices_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.invoices_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: invoices_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.invoices_id_seq OWNED BY public.invoices.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


--
-- Name: jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: payments; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.payments (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    currency character varying(255) NOT NULL,
    total_amount integer NOT NULL,
    invoice_payload character varying(255) NOT NULL,
    subscription_expiration_date timestamp(0) without time zone,
    is_recurring boolean,
    is_first_recurring boolean,
    telegram_payment_charge_id character varying(255) NOT NULL,
    provider_payment_charge_id character varying(255) NOT NULL,
    refunded_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: payments_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.payments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: payments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.payments_id_seq OWNED BY public.payments.id;


--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name text NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: pulse_aggregates; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.pulse_aggregates (
    id bigint NOT NULL,
    bucket integer NOT NULL,
    period integer NOT NULL,
    type character varying(255) NOT NULL,
    key text NOT NULL,
    key_hash uuid GENERATED ALWAYS AS ((md5(key))::uuid) STORED NOT NULL,
    aggregate character varying(255) NOT NULL,
    value numeric(20,2) NOT NULL,
    count integer
);


--
-- Name: pulse_aggregates_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.pulse_aggregates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: pulse_aggregates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.pulse_aggregates_id_seq OWNED BY public.pulse_aggregates.id;


--
-- Name: pulse_entries; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.pulse_entries (
    id bigint NOT NULL,
    "timestamp" integer NOT NULL,
    type character varying(255) NOT NULL,
    key text NOT NULL,
    key_hash uuid GENERATED ALWAYS AS ((md5(key))::uuid) STORED NOT NULL,
    value bigint
);


--
-- Name: pulse_entries_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.pulse_entries_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: pulse_entries_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.pulse_entries_id_seq OWNED BY public.pulse_entries.id;


--
-- Name: pulse_values; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.pulse_values (
    id bigint NOT NULL,
    "timestamp" integer NOT NULL,
    type character varying(255) NOT NULL,
    key text NOT NULL,
    key_hash uuid GENERATED ALWAYS AS ((md5(key))::uuid) STORED NOT NULL,
    value text NOT NULL
);


--
-- Name: pulse_values_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.pulse_values_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: pulse_values_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.pulse_values_id_seq OWNED BY public.pulse_values.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


--
-- Name: settings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.settings (
    id bigint NOT NULL,
    key character varying(255) NOT NULL,
    type character varying(255) DEFAULT 'string'::character varying NOT NULL,
    value text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: settings_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.settings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.settings_id_seq OWNED BY public.settings.id;


--
-- Name: stats; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.stats (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    period character varying(255) NOT NULL,
    period_start date NOT NULL,
    planned_count integer DEFAULT 0 NOT NULL,
    completed_count integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: stats_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.stats_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: stats_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.stats_id_seq OWNED BY public.stats.id;


--
-- Name: telescope_entries; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.telescope_entries (
    sequence bigint NOT NULL,
    uuid uuid NOT NULL,
    batch_id uuid NOT NULL,
    family_hash character varying(255),
    should_display_on_index boolean DEFAULT true NOT NULL,
    type character varying(20) NOT NULL,
    content text NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: telescope_entries_sequence_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.telescope_entries_sequence_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: telescope_entries_sequence_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.telescope_entries_sequence_seq OWNED BY public.telescope_entries.sequence;


--
-- Name: telescope_entries_tags; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.telescope_entries_tags (
    entry_uuid uuid NOT NULL,
    tag character varying(255) NOT NULL
);


--
-- Name: telescope_monitoring; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.telescope_monitoring (
    tag character varying(255) NOT NULL
);


--
-- Name: user_memories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_memories (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    category character varying(255) NOT NULL,
    content text NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: user_memories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_memories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_memories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_memories_id_seq OWNED BY public.user_memories.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    telegram_id character varying(255),
    telegram_username character varying(255),
    telegram_bot_blocked_at timestamp(0) without time zone,
    telegram_user_deleted_at timestamp(0) without time zone,
    email character varying(255),
    password character varying(255),
    email_verified_at timestamp(0) without time zone,
    is_admin boolean DEFAULT false NOT NULL,
    subscription_expires_at timestamp(0) without time zone,
    theme character varying(255) DEFAULT 'system'::character varying NOT NULL,
    timezone character varying(255),
    locale character varying(10),
    day_starts_at time(0) without time zone DEFAULT '03:00:00'::time without time zone NOT NULL,
    move_completed_to_end boolean DEFAULT true NOT NULL,
    birthdate date,
    ai_digest_time character varying(5) DEFAULT '08:00'::character varying,
    ai_tone_id bigint DEFAULT '1'::bigint,
    long_term_goal text,
    last_active_at timestamp(0) without time zone,
    trial_banner_dismissed_at timestamp(0) without time zone,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: ai_digests id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ai_digests ALTER COLUMN id SET DEFAULT nextval('public.ai_digests_id_seq'::regclass);


--
-- Name: ai_logs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ai_logs ALTER COLUMN id SET DEFAULT nextval('public.ai_logs_id_seq'::regclass);


--
-- Name: ai_models id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ai_models ALTER COLUMN id SET DEFAULT nextval('public.ai_models_id_seq'::regclass);


--
-- Name: ai_tones id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ai_tones ALTER COLUMN id SET DEFAULT nextval('public.ai_tones_id_seq'::regclass);


--
-- Name: categories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categories ALTER COLUMN id SET DEFAULT nextval('public.categories_id_seq'::regclass);


--
-- Name: category_templates id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.category_templates ALTER COLUMN id SET DEFAULT nextval('public.category_templates_id_seq'::regclass);


--
-- Name: daily_notes id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.daily_notes ALTER COLUMN id SET DEFAULT nextval('public.daily_notes_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: habit_completions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.habit_completions ALTER COLUMN id SET DEFAULT nextval('public.habit_completions_id_seq'::regclass);


--
-- Name: habit_notifications id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.habit_notifications ALTER COLUMN id SET DEFAULT nextval('public.habit_notifications_id_seq'::regclass);


--
-- Name: habit_templates id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.habit_templates ALTER COLUMN id SET DEFAULT nextval('public.habit_templates_id_seq'::regclass);


--
-- Name: habits id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.habits ALTER COLUMN id SET DEFAULT nextval('public.habits_id_seq'::regclass);


--
-- Name: invoices id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.invoices ALTER COLUMN id SET DEFAULT nextval('public.invoices_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: payments id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.payments ALTER COLUMN id SET DEFAULT nextval('public.payments_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: pulse_aggregates id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pulse_aggregates ALTER COLUMN id SET DEFAULT nextval('public.pulse_aggregates_id_seq'::regclass);


--
-- Name: pulse_entries id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pulse_entries ALTER COLUMN id SET DEFAULT nextval('public.pulse_entries_id_seq'::regclass);


--
-- Name: pulse_values id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pulse_values ALTER COLUMN id SET DEFAULT nextval('public.pulse_values_id_seq'::regclass);


--
-- Name: settings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.settings ALTER COLUMN id SET DEFAULT nextval('public.settings_id_seq'::regclass);


--
-- Name: stats id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stats ALTER COLUMN id SET DEFAULT nextval('public.stats_id_seq'::regclass);


--
-- Name: telescope_entries sequence; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.telescope_entries ALTER COLUMN sequence SET DEFAULT nextval('public.telescope_entries_sequence_seq'::regclass);


--
-- Name: user_memories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_memories ALTER COLUMN id SET DEFAULT nextval('public.user_memories_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: agent_conversation_messages agent_conversation_messages_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.agent_conversation_messages
    ADD CONSTRAINT agent_conversation_messages_pkey PRIMARY KEY (id);


--
-- Name: agent_conversations agent_conversations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.agent_conversations
    ADD CONSTRAINT agent_conversations_pkey PRIMARY KEY (id);


--
-- Name: ai_digests ai_digests_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ai_digests
    ADD CONSTRAINT ai_digests_pkey PRIMARY KEY (id);


--
-- Name: ai_digests ai_digests_user_id_date_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ai_digests
    ADD CONSTRAINT ai_digests_user_id_date_unique UNIQUE (user_id, date);


--
-- Name: ai_logs ai_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ai_logs
    ADD CONSTRAINT ai_logs_pkey PRIMARY KEY (id);


--
-- Name: ai_models ai_models_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ai_models
    ADD CONSTRAINT ai_models_pkey PRIMARY KEY (id);


--
-- Name: ai_models ai_models_slug_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ai_models
    ADD CONSTRAINT ai_models_slug_unique UNIQUE (slug);


--
-- Name: ai_tones ai_tones_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ai_tones
    ADD CONSTRAINT ai_tones_pkey PRIMARY KEY (id);


--
-- Name: ai_tones ai_tones_slug_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ai_tones
    ADD CONSTRAINT ai_tones_slug_unique UNIQUE (slug);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- Name: category_templates category_templates_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.category_templates
    ADD CONSTRAINT category_templates_pkey PRIMARY KEY (id);


--
-- Name: category_templates category_templates_slug_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.category_templates
    ADD CONSTRAINT category_templates_slug_unique UNIQUE (slug);


--
-- Name: daily_notes daily_notes_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.daily_notes
    ADD CONSTRAINT daily_notes_pkey PRIMARY KEY (id);


--
-- Name: daily_notes daily_notes_user_id_date_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.daily_notes
    ADD CONSTRAINT daily_notes_user_id_date_unique UNIQUE (user_id, date);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: habit_completions habit_completions_habit_id_completed_at_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.habit_completions
    ADD CONSTRAINT habit_completions_habit_id_completed_at_unique UNIQUE (habit_id, completed_at);


--
-- Name: habit_completions habit_completions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.habit_completions
    ADD CONSTRAINT habit_completions_pkey PRIMARY KEY (id);


--
-- Name: habit_notifications habit_notifications_habit_id_time_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.habit_notifications
    ADD CONSTRAINT habit_notifications_habit_id_time_unique UNIQUE (habit_id, "time");


--
-- Name: habit_notifications habit_notifications_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.habit_notifications
    ADD CONSTRAINT habit_notifications_pkey PRIMARY KEY (id);


--
-- Name: habit_templates habit_templates_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.habit_templates
    ADD CONSTRAINT habit_templates_pkey PRIMARY KEY (id);


--
-- Name: habits habits_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.habits
    ADD CONSTRAINT habits_pkey PRIMARY KEY (id);


--
-- Name: invoices invoices_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.invoices
    ADD CONSTRAINT invoices_pkey PRIMARY KEY (id);


--
-- Name: invoices invoices_slug_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.invoices
    ADD CONSTRAINT invoices_slug_unique UNIQUE (slug);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: payments payments_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.payments
    ADD CONSTRAINT payments_pkey PRIMARY KEY (id);


--
-- Name: payments payments_telegram_payment_charge_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.payments
    ADD CONSTRAINT payments_telegram_payment_charge_id_unique UNIQUE (telegram_payment_charge_id);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: pulse_aggregates pulse_aggregates_bucket_period_type_aggregate_key_hash_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pulse_aggregates
    ADD CONSTRAINT pulse_aggregates_bucket_period_type_aggregate_key_hash_unique UNIQUE (bucket, period, type, aggregate, key_hash);


--
-- Name: pulse_aggregates pulse_aggregates_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pulse_aggregates
    ADD CONSTRAINT pulse_aggregates_pkey PRIMARY KEY (id);


--
-- Name: pulse_entries pulse_entries_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pulse_entries
    ADD CONSTRAINT pulse_entries_pkey PRIMARY KEY (id);


--
-- Name: pulse_values pulse_values_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pulse_values
    ADD CONSTRAINT pulse_values_pkey PRIMARY KEY (id);


--
-- Name: pulse_values pulse_values_type_key_hash_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pulse_values
    ADD CONSTRAINT pulse_values_type_key_hash_unique UNIQUE (type, key_hash);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: settings settings_key_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT settings_key_unique UNIQUE (key);


--
-- Name: settings settings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT settings_pkey PRIMARY KEY (id);


--
-- Name: stats stats_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stats
    ADD CONSTRAINT stats_pkey PRIMARY KEY (id);


--
-- Name: stats stats_user_id_period_period_start_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stats
    ADD CONSTRAINT stats_user_id_period_period_start_unique UNIQUE (user_id, period, period_start);


--
-- Name: telescope_entries telescope_entries_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.telescope_entries
    ADD CONSTRAINT telescope_entries_pkey PRIMARY KEY (sequence);


--
-- Name: telescope_entries_tags telescope_entries_tags_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.telescope_entries_tags
    ADD CONSTRAINT telescope_entries_tags_pkey PRIMARY KEY (entry_uuid, tag);


--
-- Name: telescope_entries telescope_entries_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.telescope_entries
    ADD CONSTRAINT telescope_entries_uuid_unique UNIQUE (uuid);


--
-- Name: telescope_monitoring telescope_monitoring_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.telescope_monitoring
    ADD CONSTRAINT telescope_monitoring_pkey PRIMARY KEY (tag);


--
-- Name: user_memories user_memories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_memories
    ADD CONSTRAINT user_memories_pkey PRIMARY KEY (id);


--
-- Name: user_memories user_memories_user_id_category_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_memories
    ADD CONSTRAINT user_memories_user_id_category_unique UNIQUE (user_id, category);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: users users_telegram_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_telegram_id_unique UNIQUE (telegram_id);


--
-- Name: agent_conversation_messages_conversation_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX agent_conversation_messages_conversation_id_index ON public.agent_conversation_messages USING btree (conversation_id);


--
-- Name: agent_conversation_messages_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX agent_conversation_messages_user_id_index ON public.agent_conversation_messages USING btree (user_id);


--
-- Name: agent_conversations_user_id_updated_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX agent_conversations_user_id_updated_at_index ON public.agent_conversations USING btree (user_id, updated_at);


--
-- Name: ai_logs_created_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ai_logs_created_at_index ON public.ai_logs USING btree (created_at);


--
-- Name: categories_slug_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX categories_slug_index ON public.categories USING btree (slug);


--
-- Name: conversation_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX conversation_index ON public.agent_conversation_messages USING btree (conversation_id, user_id, updated_at);


--
-- Name: habit_completions_user_id_completed_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX habit_completions_user_id_completed_at_index ON public.habit_completions USING btree (user_id, completed_at);


--
-- Name: habits_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX habits_user_id_index ON public.habits USING btree (user_id);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: personal_access_tokens_expires_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX personal_access_tokens_expires_at_index ON public.personal_access_tokens USING btree (expires_at);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: pulse_aggregates_period_bucket_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX pulse_aggregates_period_bucket_index ON public.pulse_aggregates USING btree (period, bucket);


--
-- Name: pulse_aggregates_period_type_aggregate_bucket_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX pulse_aggregates_period_type_aggregate_bucket_index ON public.pulse_aggregates USING btree (period, type, aggregate, bucket);


--
-- Name: pulse_aggregates_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX pulse_aggregates_type_index ON public.pulse_aggregates USING btree (type);


--
-- Name: pulse_entries_key_hash_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX pulse_entries_key_hash_index ON public.pulse_entries USING btree (key_hash);


--
-- Name: pulse_entries_timestamp_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX pulse_entries_timestamp_index ON public.pulse_entries USING btree ("timestamp");


--
-- Name: pulse_entries_timestamp_type_key_hash_value_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX pulse_entries_timestamp_type_key_hash_value_index ON public.pulse_entries USING btree ("timestamp", type, key_hash, value);


--
-- Name: pulse_entries_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX pulse_entries_type_index ON public.pulse_entries USING btree (type);


--
-- Name: pulse_values_timestamp_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX pulse_values_timestamp_index ON public.pulse_values USING btree ("timestamp");


--
-- Name: pulse_values_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX pulse_values_type_index ON public.pulse_values USING btree (type);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: telescope_entries_batch_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX telescope_entries_batch_id_index ON public.telescope_entries USING btree (batch_id);


--
-- Name: telescope_entries_created_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX telescope_entries_created_at_index ON public.telescope_entries USING btree (created_at);


--
-- Name: telescope_entries_family_hash_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX telescope_entries_family_hash_index ON public.telescope_entries USING btree (family_hash);


--
-- Name: telescope_entries_tags_tag_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX telescope_entries_tags_tag_index ON public.telescope_entries_tags USING btree (tag);


--
-- Name: telescope_entries_type_should_display_on_index_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX telescope_entries_type_should_display_on_index_index ON public.telescope_entries USING btree (type, should_display_on_index);


--
-- Name: ai_digests ai_digests_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ai_digests
    ADD CONSTRAINT ai_digests_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: ai_logs ai_logs_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ai_logs
    ADD CONSTRAINT ai_logs_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: categories categories_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: daily_notes daily_notes_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.daily_notes
    ADD CONSTRAINT daily_notes_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: habit_completions habit_completions_habit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.habit_completions
    ADD CONSTRAINT habit_completions_habit_id_foreign FOREIGN KEY (habit_id) REFERENCES public.habits(id) ON DELETE CASCADE;


--
-- Name: habit_completions habit_completions_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.habit_completions
    ADD CONSTRAINT habit_completions_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: habit_notifications habit_notifications_habit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.habit_notifications
    ADD CONSTRAINT habit_notifications_habit_id_foreign FOREIGN KEY (habit_id) REFERENCES public.habits(id) ON DELETE CASCADE;


--
-- Name: habit_templates habit_templates_category_template_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.habit_templates
    ADD CONSTRAINT habit_templates_category_template_id_foreign FOREIGN KEY (category_template_id) REFERENCES public.category_templates(id) ON DELETE SET NULL;


--
-- Name: habits habits_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.habits
    ADD CONSTRAINT habits_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.categories(id) ON DELETE SET NULL;


--
-- Name: habits habits_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.habits
    ADD CONSTRAINT habits_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: payments payments_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.payments
    ADD CONSTRAINT payments_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: stats stats_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stats
    ADD CONSTRAINT stats_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: telescope_entries_tags telescope_entries_tags_entry_uuid_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.telescope_entries_tags
    ADD CONSTRAINT telescope_entries_tags_entry_uuid_foreign FOREIGN KEY (entry_uuid) REFERENCES public.telescope_entries(uuid) ON DELETE CASCADE;


--
-- Name: user_memories user_memories_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_memories
    ADD CONSTRAINT user_memories_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: users users_ai_tone_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_ai_tone_id_foreign FOREIGN KEY (ai_tone_id) REFERENCES public.ai_tones(id);


--
-- PostgreSQL database dump complete
--

\unrestrict dQwnvBDWhsBqHESZMqkC5dgOJs4EILvC55kW2LE2HkWDuQIYqoMkzN44aODmt1U

--
-- PostgreSQL database dump
--

\restrict dkMpaQNDfRdjMdquaC6ODGUotkGrYzYJWddNy3ckFbQNHg5WPqfGjbTLXEKQ6b5

-- Dumped from database version 18.3
-- Dumped by pg_dump version 18.4 (Ubuntu 18.4-1.pgdg24.04+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2026_04_03_235001_create_categories_table	1
5	2026_04_03_235002_create_habits_table	1
6	2026_04_03_235003_create_category_templates_table	1
7	2026_04_03_235004_create_habit_templates_table	1
8	2026_04_03_235005_create_habit_completions_table	1
9	2026_04_03_235006_create_daily_notes_table	1
10	2026_04_03_235007_create_stats_table	1
11	2026_04_03_235008_create_settings_table	1
12	2026_04_03_235009_create_habit_notifications_table	1
13	2026_04_03_235010_create_ai_tones_table	1
14	2026_04_03_235012_create_ai_digests_table	1
15	2026_04_03_235013_create_ai_logs_table	1
16	2026_04_03_235014_create_ai_models_table	1
17	2026_04_03_235015_create_user_memories_table	1
18	2026_04_03_235016_create_invoices_table	1
19	2026_04_03_235017_create_payments_table	1
20	2026_04_07_200800_create_personal_access_tokens_table	1
21	2026_04_17_154428_create_telescope_entries_table	1
22	2026_04_27_225928_create_pulse_tables	1
\.


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 25, true);


--
-- PostgreSQL database dump complete
--

\unrestrict dkMpaQNDfRdjMdquaC6ODGUotkGrYzYJWddNy3ckFbQNHg5WPqfGjbTLXEKQ6b5

