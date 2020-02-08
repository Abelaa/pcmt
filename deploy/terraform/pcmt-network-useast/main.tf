######################################################################
# Copyright (c) 2019, VillageReach
# Licensed under the Non-Profit Open Software License version 3.0.
# SPDX-License-Identifier: NPOSL-3.0
######################################################################

terraform {
  backend "s3" {
    bucket = "pcmt-terraform-states"
    key    = "pcmt-network-useast.tf"
    region = "eu-west-1"
  }
}

provider "aws" {
  region = "${var.aws-region}"
}

module "pcmt-network-useast" {
  source = "../modules/pcmt-network"

  aws-region  = "${var.aws-region}"
  tag-type    = "${var.tag-type}"
  tag-bill-to = "${var.tag-bill-to}"
}
