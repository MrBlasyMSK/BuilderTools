<?php

/**
 * Copyright (C) 2018-2021  CzechPMDevs
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace czechpmdevs\buildertools\schematics\format;

use czechpmdevs\buildertools\blockstorage\BlockArray;
use czechpmdevs\buildertools\schematics\SchematicException;
use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use function serialize;
use function unserialize;

/**
 * Experimental schematic format
 *
 * It should be used to transfer schematics between servers with BuilderTools
 * - This format is much faster than other ones.
 */
class BuilderToolsSchematic implements Schematic {

	public function load(string $rawData): BlockArray {
		$blockArray = unserialize($rawData);
		if(!$blockArray instanceof BlockArray) {
			throw new SchematicException("Invalid data provided");
		}

		return $blockArray;
	}

	public function save(BlockArray $blockArray): string {
		return serialize($blockArray);
	}

	public static function getFileExtension(): string {
		return ".btschematics";
	}

	public static function validate(string $rawData): bool {
		/** @var CompoundTag $nbt */
		$nbt = (new BigEndianNBTStream())->readCompressed($rawData);

		return $nbt->hasTag("Coords", ByteArrayTag::class) &&
			$nbt->hasTag("Blocks", ByteArrayTag::class) &&
			$nbt->hasTag("DuplicateDetection", ByteTag::class);
	}
}