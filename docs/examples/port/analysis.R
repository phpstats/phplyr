suppressPackageStartupMessages({
  library(dplyr)
  library(tidyr)
  library(lme4)
})

data <- paste0("https://raw.githubusercontent.com/phpstats",
               "/datasets/main/csv")
penguins <- read.csv(file.path(data, "penguins.csv"),
                     na.strings = c("", "NA")) |>
  as_tibble()

# Clean: drop incomplete rows, band body mass,
# compute the bill ratio.
clean <- penguins |>
  filter(!is.na(body_mass_g), !is.na(sex)) |>
  mutate(
    mass_band = case_when(
      body_mass_g < 3500 ~ "light",
      body_mass_g < 4500 ~ "mid",
      .default = "heavy"
    ),
    bill_ratio = bill_length_mm / bill_depth_mm,
    male = as.integer(sex == "male")
  )

# Reshape: one row per penguin and bill measure.
long <- clean |>
  mutate(penguin = row_number()) |>
  select(penguin, species, length = bill_length_mm,
         depth = bill_depth_mm) |>
  pivot_longer(c(length, depth), names_to = "measure",
               values_to = "mm")

long |>
  group_by(measure) |>
  summarise(mean = mean(mm), sd = sd(mm))

# Summarize: penguins per species and mass band, then
# the species by mean bill ratio.
clean |>
  count(species, mass_band) |>
  pivot_wider(names_from = mass_band, values_from = n,
              values_fill = 0)

clean |>
  group_by(species) |>
  summarise(n = n(), mean_ratio = mean(bill_ratio)) |>
  arrange(desc(mean_ratio))

# Model: linear, logistic, and random-intercept fits.
fit_lm <- lm(body_mass_g ~ flipper_length_mm + male,
             data = clean)
fit_glm <- glm(male ~ body_mass_g + bill_depth_mm,
               family = binomial, data = clean)
fit_lmer <- lmer(
  body_mass_g ~ flipper_length_mm + male + (1 | species),
  data = clean
)
round(coef(fit_lm), 4)
round(coef(fit_glm), 4)
round(fixef(fit_lmer), 4)
